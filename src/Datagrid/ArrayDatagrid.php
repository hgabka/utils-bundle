<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Doctrine\ORM\Query;
use Sonata\AdminBundle\Datagrid\Datagrid;

class ArrayDatagrid extends Datagrid
{
    public function getResults()
    {
        $this->buildPager();

        if (null === $this->results) {
            $this->results = $this->pager->getResults(Query::HYDRATE_ARRAY);
        }

        return $this->results;
    }
}
