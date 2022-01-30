<?php

namespace Hgabka\UtilsBundle\Export;

use Generator;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

abstract class AdminExporter extends EntityExporter
{
    /** @var AdminInterface */
    protected $admin;

    /**
     * @param AdminInterface $admin
     *
     * @return AdminExporter
     */
    public function setAdmin(AdminInterface $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getClass(): string
    {
        return $this->admin->getClass();
    }

    /**
     * @return Generator
     */
    public function getData(): Generator
    {
        $query = $this->createQuery();

        $doctrineQuery = $query->getDoctrineQuery();

        foreach ($doctrineQuery
                     ->toIterable() as $row) {
            yield $row;
        }
    }

    protected function createQuery(): ProxyQueryInterface
    {
        $datagrid = $this->admin->getDatagrid();
        $datagrid->buildPager();

        $query = $datagrid->getQuery();
        $alias = current($query->getQueryBuilder()->getRootAliases());

        $query->getQueryBuilder()->distinct();
        $query->getQueryBuilder()->select($alias);
        $query->setFirstResult(null);
        $query->setMaxResults(null);

        return $query;
    }
}
