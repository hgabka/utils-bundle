<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\CountOutputWalker;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;

class ArrayPager extends Pager
{
    public function computeNbResult()
    {
        $countQuery = $this->cloneQuery($this->getQuery()->getQuery());
        $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');

        $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, CountOutputWalker::class);
        $countQuery->setResultSetMapping($rsm);
        $countQuery->setFirstResult(null)->setMaxResults(null);

        return $countQuery->getSingleScalarResult();
    }

    /**
     * Clones a query.
     *
     * @param Query $query the query
     *
     * @return Query the cloned query
     */
    protected function cloneQuery(Query $query)
    {
        // @var $cloneQuery Query
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());
        $cloneQuery->setCacheable(false);

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }
}
