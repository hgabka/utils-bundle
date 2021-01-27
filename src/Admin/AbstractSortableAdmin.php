<?php

namespace Hgabka\UtilsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

abstract class AbstractSortableAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $sortField = 'position';

    /**
     * @var bool
     */
    protected $descending = false;

    /**
     * @var array
     */
    protected $datagridValues = [
        '_per_page' => \PHP_INT_MAX,
    ];

    /**
     * @var array
     */
    protected $perPageOptions = [\PHP_INT_MAX];

    /**
     * @var int
     */
    protected $maxPerPage = \PHP_INT_MAX;

    /**
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();
        $parameters['_sort_by'] = $this->sortField;
        $parameters['_sort_order'] = $this->isDescending() ? 'DESC' : 'ASC';

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

    /**
     * @param $template
     */
    public function setListTemplate($template)
    {
        $this->templates['list'] = $template;
    }

    public function isDescending(): bool
    {
        return $this->descending;
    }

    public function setDescending(bool $descending): self
    {
        $this->descending = $descending;

        return $this;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('sorting');
    }
}
