<?php

namespace Hgabka\UtilsBundle\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use PDO;

class ColumnHydrator extends AbstractHydrator
{
    const HYDRATOR_NAME = 'column';

    /**
     * Hydrators all data at once.
     *
     * @return array
     */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(PDO::FETCH_COLUMN);
    }

}