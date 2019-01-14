<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder;

class CustomDatagridBuilder extends DatagridBuilder
{
    protected function getPager($pagerType = null)
    {
        return new CustomAdminPager();
    }
}
