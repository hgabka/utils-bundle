<?php

namespace Hgabka\UtilsBundle\AdminList;

use Hgabka\UtilsBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdminList.
 */
class AdminList
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AdminListConfiguratorInterface
     */
    protected $configurator;

    /**
     * @param AdminListConfiguratorInterface $configurator The configurator
     */
    public function __construct(AdminListConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
        $this->configurator->buildFilters();
        $this->configurator->buildFields();
        $this->configurator->buildItemActions();
        $this->configurator->buildListActions();
    }

    /**
     * @return null|AdminListConfiguratorInterface
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }

    /**
     * @return FilterBuilder
     */
    public function getFilterBuilder()
    {
        return $this->configurator->getFilterBuilder();
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->configurator->bindRequest($request);
    }

    /**
     * @return Field[]
     */
    public function getColumns()
    {
        return $this->configurator->getFields();
    }

    /**
     * @return Field[]
     */
    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->configurator->getCount();
    }

    /**
     * @return null|array
     */
    public function getItems()
    {
        return $this->configurator->getItems();
    }

    /**
     * Return an iterator for all items that matches the current filtering.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->configurator->getIterator();
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function hasSort($columnName = null)
    {
        if (null === $columnName) {
            return \count($this->configurator->getSortFields()) > 0;
        }

        return \in_array($columnName, $this->configurator->getSortFields(), true);
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return $this->configurator->canEdit($item);
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return $this->configurator->canAdd();
    }

    public function canView($item)
    {
        return $this->configurator->canView($item);
    }

    /**
     * @return array
     */
    public function getIndexUrl()
    {
        return $this->configurator->getIndexUrl();
    }

    /**
     * @return array
     */
    public function getPagesizeUrl()
    {
        return $this->configurator->getPagesizeUrl();
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return $this->configurator->getEditUrlFor($item);
    }

    public function getViewUrlFor($item)
    {
        return $this->configurator->getViewUrlFor($item);
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return $this->configurator->getDeleteUrlFor($item);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params)
    {
        return $this->configurator->getAddUrlFor($params);
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return $this->configurator->canDelete($item);
    }

    /**
     * @return bool
     */
    public function canExport()
    {
        return $this->configurator->canExport();
    }

    /**
     * @return string
     */
    public function getExportUrl()
    {
        return $this->configurator->getExportUrl();
    }

    /**
     * @param array|object $object    The object
     * @param string       $attribute The attribute
     *
     * @return mixed
     */
    public function getValue($object, $attribute)
    {
        return $this->configurator->getValue($object, $attribute);
    }

    /**
     * @param array|object $object    The object
     * @param string       $attribute The attribute
     *
     * @return string
     */
    public function getStringValue($object, $attribute)
    {
        return $this->configurator->getStringValue($object, $attribute);
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->configurator->getOrderBy();
    }

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->configurator->getOrderDirection();
    }

    /**
     * @return array
     */
    public function getItemActions()
    {
        return $this->configurator->getItemActions();
    }

    /**
     * @return bool
     */
    public function hasItemActions()
    {
        return $this->configurator->hasItemActions();
    }

    /**
     * @return bool
     */
    public function hasListActions()
    {
        return $this->configurator->hasListActions();
    }

    /**
     * @return array
     */
    public function getListActions()
    {
        return $this->configurator->getListActions();
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return $this->configurator->getBulkActions();
    }

    /**
     * @return bool
     */
    public function hasBulkActions()
    {
        return $this->configurator->hasBulkActions();
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        return $this->configurator->getPagerfanta();
    }

    /**
     * Returns pagesize options.
     *
     * @return null|array
     */
    public function getPagesizeOptions()
    {
        return $this->configurator->getPagesizeOptions();
    }

    /**
     * Returns pagesize.
     *
     * @return int
     */
    public function getPagesize()
    {
        return $this->configurator->getPagesize();
    }
}
