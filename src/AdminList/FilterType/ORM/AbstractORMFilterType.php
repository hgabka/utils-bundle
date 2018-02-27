<?php

namespace Hgabka\UtilsBundle\AdminList\FilterType\ORM;

use Doctrine\ORM\QueryBuilder;
use Hgabka\UtilsBundle\AdminList\FilterType\AbstractFilterType;

/**
 * The abstract filter used for ORM query builder.
 */
abstract class AbstractORMFilterType extends AbstractFilterType
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}
