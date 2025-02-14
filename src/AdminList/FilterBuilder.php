<?php

namespace Hgabka\UtilsBundle\AdminList;

use Hgabka\UtilsBundle\AdminList\FilterType\FilterTypeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdminListFilter.
 */
class FilterBuilder
{
    /**
     * @var array
     */
    private $filterDefinitions = [];

    /**
     * @var Filter[]
     */
    private $currentFilters = [];

    /**
     * @var array
     */
    private $currentParameters = [];

    /**
     * @param string              $columnName The column name
     * @param FilterTypeInterface $type       The filter type
     * @param string              $filterName The name of the filter
     * @param array               $options    Options
     *
     * @return FilterBuilder
     */
    public function add($columnName, ?FilterTypeInterface $type = null, $filterName = null, array $options = [])
    {
        $this->filterDefinitions[$columnName] = [
            'type' => $type,
            'options' => $options,
            'filtername' => $filterName,
        ];

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return null|mixed
     */
    public function get($columnName)
    {
        if (isset($this->filterDefinitions[$columnName])) {
            return $this->filterDefinitions[$columnName];
        }

        return null;
    }

    /**
     * @param string $columnName
     *
     * @return FilterBuilder
     */
    public function remove($columnName)
    {
        if (isset($this->filterDefinitions[$columnName])) {
            unset($this->filterDefinitions[$columnName]);
        }

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function has($columnName)
    {
        return isset($this->filterDefinitions[$columnName]);
    }

    /**
     * @return array
     */
    public function getFilterDefinitions()
    {
        return $this->filterDefinitions;
    }

    public function bindRequest(Request $request, array $defaults = [])
    {
        $filterBuilderName = 'filter_' . $request->get('_route');
        $this->currentParameters = $request->query->all();
        unset($this->currentParameters['pagesize'], $this->currentParameters['folderId']);

        if (0 === \count($this->currentParameters)) {
            if (!$request->query->has('filter')) {
                if ($request->getSession()->has($filterBuilderName)) {
                    $savedQuery = $request->getSession()->get($filterBuilderName);
                    $request->query->replace($savedQuery);
                    $this->currentParameters = $savedQuery;
                } elseif (!empty($defaults)) {
                    $request->query->replace($defaults);
                    $this->currentParameters = $defaults;
                }
            }
        } else {
            $request->getSession()->set($filterBuilderName, $this->currentParameters);
        }

        $filterColumnNames = $request->query->all('filter_columnname');
        if (isset($filterColumnNames)) {
            $uniqueIds = $request->query->all('filter_uniquefilterid');
            $index = 0;
            foreach ($filterColumnNames as $filterColumnName) {
                $uniqueId = $uniqueIds[$index];
                $filter = new Filter($filterColumnName, $this->get($filterColumnName), $uniqueId);
                $this->currentFilters[] = $filter;
                $filter->bindRequest($request);
                ++$index;
            }
        }
    }

    /**
     * @return array
     */
    public function getCurrentParameters()
    {
        return $this->currentParameters;
    }

    /**
     * @return Filter[]
     */
    public function getCurrentFilters()
    {
        return $this->currentFilters;
    }
}
