<?php

namespace Hgabka\UtilsBundle\Query;

use Doctrine\ORM\QueryBuilder;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery as BaseQuery;

class ArrayAdminProxyQuery extends BaseQuery
{
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
}
