<?php

namespace Hgabka\UtilsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

abstract class AbstractSortableAdmin extends AbstractAdmin
{
    protected $sortField = 'position';

    protected $datagridValues = [
        '_per_page' => \PHP_INT_MAX,
        '_sort_direction' => 'ASC',
    ];

    protected $perPageOptions = [\PHP_INT_MAX];
    protected $maxPerPage = \PHP_INT_MAX;

    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();
        $parameters['_sort_by'] = $this->sortField;

        return $parameters;
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    /**
     * @return AbstractSortableAdmin
     */
    public function setSortField(string $sortField): self
    {
        $this->sortField = $sortField;

        return $this;
    }

    public function setListTemplate($template)
    {
        $this->templates['list'] = $template;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('sorting');
    }
}
