<?php

namespace Hgabka\UtilsBundle\Query;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface as DoctrineProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Util\SmartPaginatorFactory;

class CustomSortProxyQuery implements DoctrineProxyQueryInterface
{
    private $queryBuilder;

    /**
     * @var null|string
     */
    private $sortBy;

    /**
     * @var null|string
     */
    private $customSortBy;

    /**
     * @var null|string
     */
    private $sortOrder;

    /**
     * @var int
     */
    private $uniqueParameterId;

    /**
     * @var string[]
     */
    private $entityJoinAliases;

    /**
     * The map of query hints.
     *
     * @var array<string,mixed>
     */
    private $hints = [];

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->uniqueParameterId = 0;
        $this->entityJoinAliases = [];
    }

    /**
     * @param mixed[] $args
     *
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        return $this->queryBuilder->$name(...$args);
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->queryBuilder->$name;
    }

    public function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    /**
     * @return Paginator<object>
     */
    public function execute()
    {
        return SmartPaginatorFactory::create($this, $this->hints);
    }

    public function setSortBy(array $parentAssociationMappings, array $fieldMapping): ProxyQueryInterface
    {
        $alias = $this->entityJoin($parentAssociationMappings);
        if (\is_string($fieldMapping['fieldName'])) {
            $this->sortBy = $alias . '.' . $fieldMapping['fieldName'];
            $this->customSortBy = null;
        } else {
            $this->customSortBy = $fieldMapping['fieldName'];
            $this->sortBy = null;
        }

        return $this;
    }

    public function getDoctrineQuery(): Query
    {
        // always clone the original queryBuilder
        $queryBuilder = clone $this->queryBuilder;

        $rootAlias = current($queryBuilder->getRootAliases());

        if ($this->sortBy || $this->customSortBy) {
            $sortBy = $this->sortBy ?: $this->customSortBy;

            $priority = \is_array($sortBy) && isset($sortBy['priority']) ? $sortBy['priority'] : 'high';
            $orderByDQLPart = $queryBuilder->getDQLPart('orderBy');

            if ('high' === $priority) {
                $queryBuilder->resetDQLPart('orderBy');
            }

            if (\is_callable($sortBy)) {
                \call_user_func($sortBy, $queryBuilder, $this->getSortOrder(), $rootAlias);
            } elseif (isset($sortBy['field'])) {
                $queryBuilder->addOrderBy($rootAlias . '.' . $sortBy['field'], $this->getSortOrder());
            } elseif (isset($sortBy['callback']) && \is_callable($sortBy['callback'])) {
                \call_user_func($sortBy['callback'], $queryBuilder, $this->getSortOrder(), $rootAlias);
            } elseif (\is_string($sortBy)) {
                if (false === strpos($sortBy, '.')) { // add the current alias
                    $sortBy = $rootAlias . '.' . $sortBy;
                }
                $queryBuilder->addOrderBy($sortBy, $this->getSortOrder());
            }

            if ('high' === $priority) {
                foreach ($orderByDQLPart as $orderBy) {
                    $queryBuilder->addOrderBy($orderBy);
                }
            }
        }

        /* By default, always add a sort on the identifier fields of the first
         * used entity in the query, because RDBMS do not guarantee a
         * particular order when no ORDER BY clause is specified, or when
         * the field used for sorting is not unique.
         */

        $identifierFields = $queryBuilder
            ->getEntityManager()
            ->getMetadataFactory()
            ->getMetadataFor(current($queryBuilder->getRootEntities()))
            ->getIdentifierFieldNames();

        $existingOrders = [];
        /** @var Query\Expr\OrderBy $order */
        foreach ($queryBuilder->getDQLPart('orderBy') as $order) {
            foreach ($order->getParts() as $part) {
                $existingOrders[] = trim(str_replace([Criteria::DESC, Criteria::ASC], '', $part));
            }
        }

        foreach ($identifierFields as $identifierField) {
            $order = $rootAlias . '.' . $identifierField;
            if (!\in_array($order, $existingOrders, true)) {
                $queryBuilder->addOrderBy(
                    $order,
                    $this->getSortOrder() // reusing the sort order is the most natural way to go
                );
            }
        }

        return $queryBuilder->getQuery();
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function setSortOrder(string $sortOrder): ProxyQueryInterface
    {
        if (!\in_array(strtoupper($sortOrder), $validSortOrders = ['ASC', 'DESC'], true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid sort order, valid values are "%s"', $sortOrder, implode(', ', $validSortOrders)));
        }
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setFirstResult(?int $firstResult): ProxyQueryInterface
    {
        $this->queryBuilder->setFirstResult($firstResult);

        return $this;
    }

    public function getFirstResult(): ?int
    {
        return $this->queryBuilder->getFirstResult();
    }

    public function setMaxResults(?int $maxResults): ProxyQueryInterface
    {
        $this->queryBuilder->setMaxResults($maxResults);

        return $this;
    }

    public function getMaxResults(): ?int
    {
        return $this->queryBuilder->getMaxResults();
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }

    public function entityJoin(array $associationMappings): string
    {
        $alias = current($this->queryBuilder->getRootAliases());

        $newAlias = 's';

        $joinedEntities = $this->queryBuilder->getDQLPart('join');

        foreach ($associationMappings as $associationMapping) {
            // Do not add left join to already joined entities with custom query
            foreach ($joinedEntities as $joinExprList) {
                foreach ($joinExprList as $joinExpr) {
                    $newAliasTmp = $joinExpr->getAlias();

                    if (sprintf('%s.%s', $alias, $associationMapping['fieldName']) === $joinExpr->getJoin()) {
                        $this->entityJoinAliases[] = $newAliasTmp;
                        $alias = $newAliasTmp;

                        continue 3;
                    }
                }
            }

            $newAlias .= '_' . $associationMapping['fieldName'];
            if (!\in_array($newAlias, $this->entityJoinAliases, true)) {
                $this->entityJoinAliases[] = $newAlias;
                $this->queryBuilder->leftJoin(sprintf('%s.%s', $alias, $associationMapping['fieldName']), $newAlias);
            }

            $alias = $newAlias;
        }

        return $alias;
    }

    /**
     * Sets a {@see \Doctrine\ORM\Query} hint. If the hint name is not recognized, it is silently ignored.
     *
     * @param string $name  the name of the hint
     * @param mixed  $value the value of the hint
     *
     * @see \Doctrine\ORM\Query::setHint
     * @see \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER
     */
    public function setHint(string $name, $value): ProxyQueryInterface
    {
        $this->hints[$name] = $value;

        return $this;
    }
}
