<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Doctrine\ORM\Query;
use Hgabka\UtilsBundle\Doctrine\Hydrator\CountHydrator;
use Hgabka\UtilsBundle\Doctrine\Query\CountSqlWalker;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;

class CustomAdminPager extends Pager
{
    public function computeNbResult()
    {
        /* @var $countQuery Query */
        $countQuery = clone $this->getQuery();
        $countQuery->setParameters($this->getQuery()->getParameters());
        $countQuery = $countQuery->getQuery();
        
        $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, CountSqlWalker::class);

        return $countQuery->getResult(CountHydrator::HYDRATOR_NAME);
    }
}
