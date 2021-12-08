<?php

namespace Hgabka\UtilsBundle\AdminList\Configurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Hgabka\UtilsBundle\AdminList\FilterType\DBAL\AbstractDBALFilterType;
use Hgabka\UtilsBundle\Helper\DoctrineDBALAdapter;
use Pagerfanta\Pagerfanta;
use Traversable;

/**
 * An abstract admin list configurator that can be used with dbal query builder.
 */
abstract class AbstractDoctrineDBALAdminListConfigurator extends AbstractAdminListConfigurator
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var Pagerfanta
     */
    private $pagerfanta;

    /**
     * @var string
     */
    private $countField = 'b.id';

    /**
     * @var bool
     */
    private $useDistinctCount = true;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return the url to edit the given $item.
     *
     * @param array $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        $params = ['id' => $item['id']];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_EDIT),
            'params' => $params,
        ];
    }

    /**
     * Get the delete url for the given $item.
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = ['id' => $item['id']];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params' => $params,
        ];
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        if (null === $this->pagerfanta) {
            $adapter = new DoctrineDBALAdapter(
                $this->getQueryBuilder(),
                $this->getCountField(),
                $this->getUseDistinctCount()
            );
            $this->pagerfanta = new Pagerfanta($adapter);
            $this->pagerfanta->setMaxPerPage($this->getPagesize());
            $this->pagerfanta->setCurrentPage($this->getPage());
        }

        return $this->pagerfanta;
    }

    public function adaptQueryBuilder(
        QueryBuilder $queryBuilder,
        // @noinspection PhpUnusedParameterInspection
        array $params = []
    ) {
        $queryBuilder->where('1=1');
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    /**
     * @return array|mixed|Traversable
     */
    public function getItems()
    {
        return $this->getPagerfanta()->getCurrentPageResults();
    }

    /**
     * Return an iterator for all items that matches the current filtering.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        /** @var Statement $statement */
        $statement = $this->getQueryBuilder()->execute();

        return $statement;
    }

    /**
     * @return null|QueryBuilder
     */
    public function getQueryBuilder()
    {
        if (null === $this->queryBuilder) {
            $this->queryBuilder = new QueryBuilder($this->connection);
            $this->adaptQueryBuilder($this->queryBuilder);

            // Apply filters
            $filters = $this->getFilterBuilder()->getCurrentFilters();
            foreach ($filters as $filter) {
                // @var AbstractDBALFilterType $type
                $type = $filter->getType();
                $type->setQueryBuilder($this->queryBuilder);
                $filter->apply();
            }

            // Apply sorting
            if (!empty($this->orderBy)) {
                $orderBy = $this->orderBy;
                $this->queryBuilder->orderBy($orderBy, ('DESC' === $this->orderDirection ? 'DESC' : 'ASC'));
            }
        }

        return $this->queryBuilder;
    }

    /**
     * Set count field (must include table alias!).
     *
     * @param string $countField
     *
     * @return AbstractDoctrineDBALAdminListConfigurator
     */
    public function setCountField($countField)
    {
        $this->countField = $countField;

        return $this;
    }

    /**
     * Get current count field (including table alias).
     *
     * @return string
     */
    public function getCountField()
    {
        return $this->countField;
    }

    /**
     * When doing the count you can turn the distinct on or off.
     *
     * @param bool $value
     *
     * @return AbstractDoctrineDBALAdminListConfigurator
     */
    public function setUseDistinctCount($value)
    {
        $this->useDistinctCount = $value;

        return $this;
    }

    /**
     * Get current doDistinctCount.
     *
     * @return bool
     */
    public function getUseDistinctCount()
    {
        return $this->useDistinctCount;
    }
}
