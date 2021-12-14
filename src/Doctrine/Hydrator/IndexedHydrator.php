<?php

namespace Hgabka\UtilsBundle\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class IndexedHydrator extends AbstractHydrator
{
    public const HYDRATOR_NAME = 'indexed';

    /**
     * Hydrators all data at once.
     *
     * @return array
     */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAllAssociativeIndexed();
    }
}
