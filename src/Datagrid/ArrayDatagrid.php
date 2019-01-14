<?php

namespace Hgabka\UtilsBundle\Datagrid;

use Doctrine\ORM\Query;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Datagrid\Datagrid;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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

    public function buildPager()
    {
        if ($this->bound) {
            return;
        }

        foreach ($this->getFilters() as $name => $filter) {
            [$type, $options] = $filter->getRenderSettings();

            $this->formBuilder->add($filter->getFormName(), $type, $options);
        }

        $hiddenType = HiddenType::class;

        $this->formBuilder->add('_sort_by', $hiddenType);
        $this->formBuilder->get('_sort_by')->addViewTransformer(new CallbackTransformer(
            function ($value) {
                return $value;
            },
            function ($value) {
                return $value instanceof FieldDescriptionInterface ? $value->getName() : $value;
            }
        ));

        $this->formBuilder->add('_sort_order', $hiddenType);
        $this->formBuilder->add('_page', $hiddenType);
        $this->formBuilder->add('_per_page', $hiddenType);

        $this->form = $this->formBuilder->getForm();
        $this->form->submit($this->values);

        $data = $this->form->getData();

        foreach ($this->getFilters() as $name => $filter) {
            $this->values[$name] = isset($this->values[$name]) ? $this->values[$name] : null;
            $filterFormName = $filter->getFormName();
            if (isset($this->values[$filterFormName]['value']) && '' !== $this->values[$filterFormName]['value']) {
                $filter->apply($this->query, $data[$filterFormName]);
            }
        }

        if (isset($this->values['_sort_by'])) {
            if (!$this->values['_sort_by'] instanceof FieldDescriptionInterface) {
                throw new UnexpectedTypeException($this->values['_sort_by'], FieldDescriptionInterface::class);
            }

            if ($this->values['_sort_by']->isSortable()) {
                $fieldMap = $this->values['_sort_by']->getSortFieldMapping();
                if (!$fieldMap) {
                    $fieldMap = ['fieldName' => $this->values['_sort_by']->getFieldName()];
                }
                $this->query->setSortBy($this->values['_sort_by']->getSortParentAssociationMapping(), $fieldMap);
                $this->query->setSortOrder(isset($this->values['_sort_order']) ? $this->values['_sort_order'] : null);
            }
        }

        $maxPerPage = 25;
        if (isset($this->values['_per_page'])) {
            if (isset($this->values['_per_page']['value'])) {
                $maxPerPage = $this->values['_per_page']['value'];
            } else {
                $maxPerPage = $this->values['_per_page'];
            }
        }
        $this->pager->setMaxPerPage($maxPerPage);

        $page = 1;
        if (isset($this->values['_page'])) {
            if (isset($this->values['_page']['value'])) {
                $page = $this->values['_page']['value'];
            } else {
                $page = $this->values['_page'];
            }
        }

        $this->pager->setPage($page);

        $this->pager->setQuery($this->query);
        $this->pager->init();

        $this->bound = true;
    }
}
