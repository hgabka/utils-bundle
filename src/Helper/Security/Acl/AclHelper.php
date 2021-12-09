<?php

namespace Hgabka\UtilsBundle\Helper\Security\Acl;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use InvalidArgumentException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * AclHelper is a helper class to help setting the permissions when querying using ORM.
 *
 * @see https://gist.github.com/1363377
 */
class AclHelper
{
    /** @var ObjectIdentityRetrievalStrategy */
    protected $oiaStrategy;

    /** @var MutableAclProviderInterface */
    protected $aclProvider;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var QuoteStrategy
     */
    private $quoteStrategy;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * Constructor.
     *
     * @param EntityManager          $em           The entity manager
     * @param TokenStorageInterface  $tokenStorage The security token storage
     * @param RoleHierarchyInterface $rh           The role hierarchies
     */
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, RoleHierarchyInterface $rh, ObjectIdentityRetrievalStrategy $oiaStrategy, MutableAclProviderInterface $aclProvider)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->quoteStrategy = $em->getConfiguration()->getQuoteStrategy();
        $this->roleHierarchy = $rh;
        $this->oiaStrategy = $oiaStrategy;
        $this->aclProvider = $aclProvider;
    }

    /**
     * Apply the ACL constraints to the specified query builder, using the permission definition.
     *
     * @param QueryBuilder         $queryBuilder  The query builder
     * @param PermissionDefinition $permissionDef The permission definition
     *
     * @return Query
     */
    public function apply(QueryBuilder $queryBuilder, PermissionDefinition $permissionDef)
    {
        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $queryBuilder->where('1 = 1'); // this will help in cases where no where query is specified
        }

        $query = $this->cloneQuery($queryBuilder->getQuery());

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = \constant(\get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, AclWalker::class);

        $rootEntity = $permissionDef->getEntity();
        $rootAlias = $permissionDef->getAlias();
        // If either alias or entity was not specified - use default from QueryBuilder
        if (empty($rootEntity) || empty($rootAlias)) {
            $rootEntities = $queryBuilder->getRootEntities();
            $rootAliases = $queryBuilder->getRootAliases();
            $rootEntity = $rootEntities[0];
            $rootAlias = $rootAliases[0];
        }
        $query->setHint('acl.root.entity', $rootEntity);
        $query->setHint('acl.extra.query', $this->getPermittedAclIdsSQLForUser($query));

        $classMeta = $this->em->getClassMetadata($rootEntity);
        $entityRootTableName = $this->quoteStrategy->getTableName(
            $classMeta,
            $this->em->getConnection()->getDatabasePlatform()
        );
        $query->setHint('acl.entityRootTableName', $entityRootTableName);
        $query->setHint('acl.entityRootTableDqlAlias', $rootAlias);

        return $query;
    }

    /**
     * Apply the ACL constraints to the specified query builder, using the permission definition.
     *
     * @param QueryBuilder         $queryBuilder  The query builder
     * @param PermissionDefinition $permissionDef The permission definition
     *
     * @return Query
     */
    public function applyToProxyQuery(ProxyQueryInterface $query, PermissionDefinition $permissionDef): ProxyQueryInterface
    {
        $queryBuilder = $query->getQueryBuilder();
        $whereQueryParts = $queryBuilder->getDQLPart('where');
        if (empty($whereQueryParts)) {
            $queryBuilder->where('1 = 1'); // this will help in cases where no where query is specified
        }

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = \constant(\get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $query->setHint('acl.mask', $builder->get());
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, AclWalker::class);

        $rootEntity = $permissionDef->getEntity();
        $rootAlias = $permissionDef->getAlias();
        // If either alias or entity was not specified - use default from QueryBuilder
        if (empty($rootEntity) || empty($rootAlias)) {
            $rootEntities = $queryBuilder->getRootEntities();
            $rootAliases = $queryBuilder->getRootAliases();
            $rootEntity = $rootEntities[0];
            $rootAlias = $rootAliases[0];
        }
        $query->setHint('acl.root.entity', $rootEntity);
        $query->setHint('acl.extra.query', $this->getPermittedAclIdsSQLForUser($query));

        $classMeta = $this->em->getClassMetadata($rootEntity);
        $entityRootTableName = $this->quoteStrategy->getTableName(
            $classMeta,
            $this->em->getConnection()->getDatabasePlatform()
        );
        $query->setHint('acl.entityRootTableName', $entityRootTableName);
        $query->setHint('acl.entityRootTableDqlAlias', $rootAlias);

        return $query;
    }

    /**
     * Returns valid IDs for a specific entity with ACL restrictions for current user applied.
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getAllowedEntityIds(PermissionDefinition $permissionDef)
    {
        $rootEntity = $permissionDef->getEntity();
        if (empty($rootEntity)) {
            throw new InvalidArgumentException('You have to provide an entity class name!');
        }
        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = \constant(\get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }

        $query = new Query($this->em);
        $query->setHint('acl.mask', $builder->get());
        $query->setHint('acl.root.entity', $rootEntity);
        $sql = $this->getPermittedAclIdsSQLForUser($query);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $nativeQuery = $this->em->createNativeQuery($sql, $rsm);

        $transform = function ($item) {
            return $item['id'];
        };
        $result = array_map($transform, $nativeQuery->getScalarResult());

        return $result;
    }

    /**
     * @return null|TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @param $originalNode
     * @param $nodeNewPage
     */
    public function updateAcl($originalNode, $nodeNewPage)
    {
        // @var MutableAclProviderInterface $aclProvider
        $aclProvider = $this->aclProvider;
        // @var ObjectIdentityRetrievalStrategyInterface $strategy
        $strategy = $this->oiaStrategy;
        $originalIdentity = $strategy->getObjectIdentity($originalNode);
        $originalAcl = $aclProvider->findAcl($originalIdentity);

        $newIdentity = $strategy->getObjectIdentity($nodeNewPage);
        $newAcl = $aclProvider->createAcl($newIdentity);

        $aces = $originalAcl->getObjectAces();
        // @var EntryInterface $ace
        foreach ($aces as $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                $newAcl->insertObjectAce($securityIdentity, $ace->getMask());
            }
        }
        $aclProvider->updateAcl($newAcl);
    }

    /**
     * Clone specified query with parameters.
     *
     * @return Query
     */
    protected function cloneQuery(Query $query)
    {
        $aclAppliedQuery = clone $query;
        $params = $query->getParameters();
        // @var $param Parameter
        foreach ($params as $param) {
            $aclAppliedQuery->setParameter($param->getName(), $param->getValue(), $param->getType());
        }

        return $aclAppliedQuery;
    }

    /**
     * This query works well with small offset, but if want to use it with large offsets please refer to the link on how to implement
     * http://www.scribd.com/doc/14683263/Efficient-Pagination-Using-MySQL
     * This will only check permissions on the first entity added in the from clause, it will not check permissions
     * By default the number of rows returned are 10 starting from 0.
     *
     * @return string
     */
    private function getPermittedAclIdsSQLForUser(Query $query)
    {
        $aclConnection = $this->em->getConnection();
        $databasePrefix = is_file($aclConnection->getDatabase()) ? '' : $aclConnection->getDatabase() . '.';
        $mask = $query->getHint('acl.mask');
        $rootEntity = '"' . str_replace('\\', '\\\\', $query->getHint('acl.root.entity')) . '"';

        // @var $token TokenInterface
        $token = $this->tokenStorage->getToken();
        $userRoles = [];
        $user = null;
        if (null !== $token) {
            $user = $token->getUser();
            $userRoles = $this->roleHierarchy->getReachableRoleNames($token->getRoleNames());
        }

        // Security context does not provide anonymous role automatically.
        $uR = ['"IS_AUTHENTICATED_ANONYMOUSLY"'];

        // @var $role RoleInterface
        foreach ($userRoles as $role) {
            // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
            if ('ROLE_USER' !== $role) {
                $uR[] = '"' . $role . '"';
            }
        }
        $uR = array_unique($uR);
        $inString = implode(' OR s.identifier = ', $uR);

        if (\is_object($user)) {
            $inString .= ' OR s.identifier = "' . str_replace(
                '\\',
                '\\\\',
                \get_class($user)
            ) . '-' . $user->getUserName() . '"';
        }

        $selectQuery = <<<SELECTQUERY
            SELECT DISTINCT o.object_identifier as id FROM {$databasePrefix}acl_object_identities as o
            INNER JOIN {$databasePrefix}acl_classes c ON c.id = o.class_id
            LEFT JOIN {$databasePrefix}acl_entries e ON (
                e.class_id = o.class_id AND (e.object_identity_id = o.id
                OR {$aclConnection->getDatabasePlatform()->getIsNullExpression('e.object_identity_id')})
            )
            LEFT JOIN {$databasePrefix}acl_security_identities s ON (
            s.id = e.security_identity_id
            )
            WHERE c.class_type = {$rootEntity}
            AND (s.identifier = {$inString})
            AND e.mask & {$mask} > 0
            SELECTQUERY;

        return $selectQuery;
    }
}
