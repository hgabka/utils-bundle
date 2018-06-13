<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class ArrayDatagridBuilder extends DatagridBuilder
{
    public function getBaseDatagrid(AdminInterface $admin, array $values = [])
    {
        $pager = $this->getPager($admin->getPagerType());

        $pager->setCountColumn($admin->getModelManager()->getIdentifierFieldNames($admin->getClass()));

        $defaultOptions = [];
        if ($this->csrfTokenEnabled) {
            $defaultOptions['csrf_protection'] = false;
        }

        $formBuilder = $this->formFactory->createNamedBuilder('filter', FormType::class, [], $defaultOptions);

        return new ArrayDatagrid($admin->createQuery(), $admin->getList(), $pager, $formBuilder, $values);
    }

    protected function getPager($pagerType)
    {
        return new ArrayPager();
    }
}
