<?php

namespace Hgabka\UtilsBundle\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class ColumnHydrator extends AbstractHydrator
{
    public const HYDRATOR_NAME = 'column';

    /**
     * Hydrators all data at once.
     *
     * @return array
     */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchFirstColumn();
    }
}
