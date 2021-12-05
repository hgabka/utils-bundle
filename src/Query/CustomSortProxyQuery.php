<?php

namespace Hgabka\UtilsBundle\Query;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery as BaseQuery;

class CustomSortProxyQuery extends BaseQuery
{
    /**
     * The map of query hints.
     *
     * @var array<string,mixed>
     */
    private $hints = [];

    public function setSortBy($parentAssociationMappings, $fieldMapping)
    {
        $alias = $this->entityJoin($parentAssociationMappings);
        if (\is_string($fieldMapping['fieldName'])) {
            $this->sortBy = $alias . '.' . $fieldMapping['fieldName'];
        } else {
            $this->sortBy = $fieldMapping['fieldName'];
        }

        return $this;
    }

    public function getDoctrineQuery(): Query
    {
        // always clone the original queryBuilder
        $queryBuilder = clone $this->queryBuilder;

        $rootAlias = current($queryBuilder->getRootAliases());

        if ($this->getSortBy()) {
            $sortBy = $this->getSortBy();
            $priority = \is_array($sortBy) && isset($sortBy['priority']) ? $sortBy['priority'] : 'high';
            $orderByDQLPart = $queryBuilder->getDQLPart('orderBy');

            if ('high' === $priority) {
                $queryBuilder->resetDQLPart('orderBy');
            }

            if (\is_callable($sortBy)) {
                \call_user_func($sortBy, $queryBuilder, $this->getSortOrder(), $rootAlias);
            } elseif (isset($sortBy['field'])) {
                $queryBuilder->addOrderBy($rootAlias . '.' . $sortBy['field'], $this->getSortOrder());
            } elseif (isset($sortBy['callback']) && \is_callable($sortBy['callback'])) {
                \call_user_func($sortBy['callback'], $queryBuilder, $this->getSortOrder(), $rootAlias);
            } elseif (\is_string($sortBy)) {
                if (false === strpos($sortBy, '.')) { // add the current alias
                    $sortBy = $rootAlias . '.' . $sortBy;
                }
                $queryBuilder->addOrderBy($sortBy, $this->getSortOrder());
            }

            if ('high' === $priority) {
                foreach ($orderByDQLPart as $orderBy) {
                    $queryBuilder->addOrderBy($orderBy);
                }
            }
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
            $order = $rootAlias . '.' . $identifierField;
            if (!\in_array($order, $existingOrders, true)) {
                $queryBuilder->addOrderBy(
                    $order,
                    $this->getSortOrder() // reusing the sort order is the most natural way to go
                );
            }
        }

        return $queryBuilder->getQuery();
    }
}
