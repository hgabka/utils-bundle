<?php

namespace Hgabka\UtilsBundle\Query;

use Doctrine\ORM\QueryBuilder;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery as BaseQuery;
use Doctrine\Common\Collections\Criteria;

class ArrayAdminProxyQuery extends BaseQuery
{

    /**
     * The map of query hints.
     *
     * @var array<string,mixed>
     */
    private $hints = [];

    /**
     * This method alters the query to return a clean set of object with a working
     * set of Object.
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    protected function getFixedQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $queryBuilder;
    }

    public function execute(array $params = [], $hydrationMode = null)
    {
        // always clone the original queryBuilder
        $queryBuilder = clone $this->queryBuilder;

        $rootAlias = current($queryBuilder->getRootAliases());

        // todo : check how doctrine behave, potential SQL injection here ...
        if ($this->getSortBy()) {
            $sortBy = $this->getSortBy();
            if (false === strpos($sortBy, '.')) { // add the current alias
                if (!$this->isCustomField($sortBy)) {
                    $sortBy = $rootAlias . '.' . $sortBy;
                }
            }
            $queryBuilder->addOrderBy($sortBy, $this->getSortOrder());
        } else {
            $queryBuilder->resetDQLPart('orderBy');
        }

        /* By default, always add a sort on the identifier fields of the first
         * used entity in the query, because RDBMS do not guarantee a
         * particular order when no ORDER BY clause is specified, or when
         * the field used for sorting is not unique.
         */

        $identifierFields = $queryBuilder
            ->getEntityManager()
            ->getMetadataFactory()
            ->getMetadataFor(current($queryBuilder->getRootEntities()))
            ->getIdentifierFieldNames();

        $existingOrders = [];
        /** @var Query\Expr\OrderBy $order */
        foreach ($queryBuilder->getDQLPart('orderBy') as $order) {
            foreach ($order->getParts() as $part) {
                $existingOrders[] = trim(str_replace([Criteria::DESC, Criteria::ASC], '', $part));
            }
        }

        foreach ($identifierFields as $identifierField) {
            $order = $rootAlias.'.'.$identifierField;
            if (!in_array($order, $existingOrders)) {
                $queryBuilder->addOrderBy(
                    $order,
                    $this->getSortOrder() // reusing the sort order is the most natural way to go
                );
            }
        }

        $query = $this->getFixedQueryBuilder($queryBuilder)->getQuery();
        foreach ($this->hints as $name => $value) {
            $query->setHint($name, $value);
        }

        return $query->execute($params, $hydrationMode);
    }

    public function setSortBy($parentAssociationMappings, $fieldMapping)
    {
        $alias = $this->entityJoin($parentAssociationMappings);
        if ($this->isCustomField($fieldMapping['fieldName'])) {
            $this->sortBy = $fieldMapping['fieldName'];
        } else {
            $this->sortBy = $alias . '.' . $fieldMapping['fieldName'];
        }

        return $this;
    }

    protected function isCustomField($field)
    {
        foreach ($query = $this->getFixedQueryBuilder($this->queryBuilder)->getDQLPart('select') as $select) {
            foreach ($select->getParts() as $selectPart) {
                if (strpos(strtoupper($selectPart), 'AS '.$field) || strpos(strtoupper($selectPart), 'AS HIDDEN '.$field)) {
                    return true;
                }
            }
        }

        return false;
    }
}
