<?php

namespace Hgabka\UtilsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

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

    public function isDescending(): bool
    {
        return $this->descending;
    }

    public function getPerPageOptions(): array
    {
        return [];
    }

    public function setDescending(bool $descending): self
    {
        $this->descending = $descending;

        return $this;
    }

    public function isFiltered()
    {
        $filters = $this->getFilterPersister()->get($this->getCode());

        unset($filters['_sort_by'], $filters['_sort_order'], $filters['_per_page'], $filters['_page']);

        return !empty($filters);
    }

    protected function configureDefaultSortValues(array & $sortValues): void
    {
        $sortValues[DatagridInterface::PER_PAGE] = \PHP_INT_MAX;
        $sortValues[DatagridInterface::SORT_BY] = $this->sortField;
        $sortValues[DatagridInterface::SORT_ORDER] = $this->isDescending() ? 'DESC' : 'ASC';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('sorting');
    }

    protected function setListTemplate()
    {
        $this->getTemplateRegistry()->setTemplate('list', '@HgabkaUtils/Admin/Sortable/base_list.html.twig');
    }

    protected function setResultsTemplate()
    {
        $this->getTemplateRegistry()->setTemplate('pager_results', '@HgabkaUtils/Admin/Sortable/base_results.html.twig');
    }

    protected function configure(): void
    {
        $this->setListTemplate();
        $this->setResultsTemplate();
    }
}
