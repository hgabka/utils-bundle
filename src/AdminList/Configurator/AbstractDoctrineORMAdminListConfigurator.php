<?php

namespace Hgabka\UtilsBundle\AdminList\Configurator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Hgabka\UtilsBundle\AdminList\Filter;
use Hgabka\UtilsBundle\AdminList\FilterType\ORM\AbstractORMFilterType;
use Hgabka\UtilsBundle\AdminList\SortableInterface;
use Hgabka\UtilsBundle\Helper\Security\Acl\AclHelper;
use Hgabka\UtilsBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Traversable;

/**
 * An abstract admin list configurator that can be used with the orm query builder.
 */
abstract class AbstractDoctrineORMAdminListConfigurator extends AbstractAdminListConfigurator
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var Pagerfanta
     */
    private $pagerfanta;

    /**
     * @var PermissionDefinition
     */
    private $permissionDef;

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        $this->em = $em;
        $this->aclHelper = $aclHelper;
    }

    /**
     * Return the url to edit the given $item.
     *
     * @param object $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        $params = ['id' => $item->getId()];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_EDIT),
            'params' => $params,
        ];
    }

    /**
     * Get the delete url for the given $item.
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = ['id' => $item->getId()];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params' => $params,
        ];
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        if (null === $this->pagerfanta) {
            $adapter = new QueryAdapter($this->getQuery());
            $this->pagerfanta = new Pagerfanta($adapter);
            $this->pagerfanta->setNormalizeOutOfRangePages(true);
            $this->pagerfanta->setMaxPerPage($this->getPagesize());
            $this->pagerfanta->setCurrentPage($this->getPage());
        }

        return $this->pagerfanta;
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->where('1=1');
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    /**
     * @return array|Traversable
     */
    public function getItems()
    {
        return $this->getPagerfanta()->getCurrentPageResults();
    }

    /**
     * Return an iterator for all items that matches the current filtering.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->getQuery()->iterate();
    }

    /**
     * @return null|Query
     */
    public function getQuery()
    {
        if (null === $this->query) {
            $queryBuilder = $this->getQueryBuilder();
            $this->adaptQueryBuilder($queryBuilder);

            // Apply filters
            $filters = $this->getFilterBuilder()->getCurrentFilters();
            // @var Filter $filter
            foreach ($filters as $filter) {
                // @var AbstractORMFilterType $type
                $type = $filter->getType();
                $type->setQueryBuilder($queryBuilder);
                $filter->apply();
            }

            // Apply sorting
            if (!empty($this->orderBy)) {
                $orderBy = $this->getOrderByColumn();
                $queryBuilder->orderBy($orderBy, ('DESC' === $this->orderDirection ? 'DESC' : 'ASC'));
            }

            // Apply other changes
            $this->finishQueryBuilder($queryBuilder);

            // Apply ACL restrictions (if applicable)
            if (null !== $this->permissionDef && null !== $this->aclHelper) {
                $this->query = $this->aclHelper->apply($queryBuilder, $this->permissionDef);
            } else {
                $this->query = $queryBuilder->getQuery();
            }
        }

        return $this->query;
    }

    /**
     * Get current permission definition.
     *
     * @return null|PermissionDefinition
     */
    public function getPermissionDefinition()
    {
        return $this->permissionDef;
    }

    /**
     * Set permission definition.
     *
     * @return AbstractDoctrineORMAdminListConfigurator
     */
    public function setPermissionDefinition(PermissionDefinition $permissionDef)
    {
        $this->permissionDef = $permissionDef;

        return $this;
    }

    /**
     * @param EntityManager $em
     *
     * @return AbstractDoctrineORMAdminListConfigurator
     */
    public function setEntityManager($em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    protected function getOrderByColumn()
    {
        $orderBy = $this->orderBy;
        if (!strpos($orderBy, '.')) {
            $orderBy = 'b.' . $orderBy;
        }

        return $orderBy;
    }

    protected function finishQueryBuilder(QueryBuilder $queryBuilder)
    {
        if ($this instanceof SortableInterface) {
            $queryBuilder->addOrderBy('b.' . $this->getSortableField());
        }
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $queryBuilder = $this->em
            ->getRepository($this->getRepositoryName())
            ->createQueryBuilder('b');

        return $queryBuilder;
    }
}
