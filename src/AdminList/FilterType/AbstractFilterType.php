<?php

namespace Hgabka\UtilsBundle\AdminList\FilterType;

/**
 * AbstractFilterType.
 *
 * Abstract base class for all admin list filters
 */
abstract class AbstractFilterType implements FilterTypeInterface
{
    /**
     * @var null|string
     */
    protected $columnName;

    /**
     * @var null|string
     */
    protected $alias;

    /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function __construct($columnName, $alias = 'b')
    {
        $this->columnName = $columnName;
        $this->alias = $alias;
    }

    /**
     * Returns empty string if no alias, otherwise make sure the alias has just one '.' after it.
     *
     * @return string
     */
    protected function getAlias()
    {
        if (empty($this->alias)) {
            return '';
        }

        if (false !== strpos($this->alias, '.')) {
            return $this->alias;
        }

        return $this->alias . '.';
    }
}
