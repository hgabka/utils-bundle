<?php

namespace Hgabka\UtilsBundle\Helper\Security\Acl;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * AclHelper is a helper class to help setting the permissions when querying using native queries.
 *
 * @see https://gist.github.com/1363377
 */
class AclNativeHelper
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /** @var string */
    private $publicAccessRole;

    /**
     * Constructor.
     *
     * @param EntityManager          $em           The entity manager
     * @param TokenStorageInterface  $tokenStorage The security context
     * @param RoleHierarchyInterface $rh           The role hierarchies
     */
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, RoleHierarchyInterface $rh, string $publicAccessRole)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->roleHierarchy = $rh;
        $this->publicAccessRole = $publicAccessRole;
    }

    /**
     * Apply the ACL constraints to the specified query builder, using the permission definition.
     *
     * @param QueryBuilder         $queryBuilder  The query builder
     * @param PermissionDefinition $permissionDef The permission definition
     *
     * @return QueryBuilder
     */
    public function apply(QueryBuilder $queryBuilder, PermissionDefinition $permissionDef)
    {
        $aclConnection = $this->em->getConnection();

        $databasePrefix = is_file($aclConnection->getDatabase()) ? '' : $aclConnection->getDatabase() . '.';
        $rootEntity = $permissionDef->getEntity();
        $linkAlias = $permissionDef->getAlias();
        // Only tables with a single ID PK are currently supported
        $linkField = $this->em->getClassMetadata($rootEntity)->getSingleIdentifierColumnName();

        $rootEntity = '"' . str_replace('\\', '\\\\', $rootEntity) . '"';
        $query = $queryBuilder;

        $builder = new MaskBuilder();
        foreach ($permissionDef->getPermissions() as $permission) {
            $mask = \constant(\get_class($builder) . '::MASK_' . strtoupper($permission));
            $builder->add($mask);
        }
        $mask = $builder->get();

        // @var $token TokenInterface
        $token = $this->tokenStorage->getToken();
        $userRoles = [];
        if (null !== $token) {
            $user = $token->getUser();
            $userRoles = $this->roleHierarchy->getReachableRoleNames($token->getRoleNames());
        }

        // Security context does not provide anonymous role automatically.
        $uR = ['"' . $this->publicAccessRole . '"'];

        // @var $role RoleInterface
        foreach ($userRoles as $role) {
            // The reason we ignore this is because by default FOSUserBundle adds ROLE_USER for every user
            if ('ROLE_USER' !== $role) {
                $uR[] = '"' . $role . '"';
            }
        }
        $uR = array_unique($uR);
        $inString = implode(' OR s.identifier = ', (array) $uR);

        if (\is_object($user)) {
            $inString .= ' OR s.identifier = "' . str_replace(
                '\\',
                '\\\\',
                \get_class($user)
            ) . '-' . $user->getUserName() . '"';
        }

        $joinTableQuery = <<<SELECTQUERY
            SELECT DISTINCT o.object_identifier as id FROM {$databasePrefix}acl_object_identities as o
            INNER JOIN {$databasePrefix}acl_classes c ON c.id = o.class_id
            LEFT JOIN {$databasePrefix}acl_entries e ON (
                e.class_id = o.class_id AND (e.object_identity_id = o.id
                OR e.object_identity_id IS NULL)
            )
            LEFT JOIN {$databasePrefix}acl_security_identities s ON (
            s.id = e.security_identity_id
            )
            WHERE c.class_type = {$rootEntity}
            AND (s.identifier = {$inString})
            AND e.mask & {$mask} > 0
            SELECTQUERY;

        $query->join($linkAlias, '(' . $joinTableQuery . ')', 'perms_', 'perms_.id = ' . $linkAlias . '.' . $linkField);

        return $query;
    }

    /**
     * @return null|TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }
}
