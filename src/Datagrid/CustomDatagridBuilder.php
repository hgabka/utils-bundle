<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Hgabka\UtilsBundle\Datagrid\CustomAdminPager;

class CustomDatagridBuilder extends DatagridBuilder
{
    protected function getPager($pagerType = null)
    {
        return new CustomAdminPager();
    }
}
