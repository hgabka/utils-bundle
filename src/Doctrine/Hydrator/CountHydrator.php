<?php


namespace Hgabka\UtilsBundle\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class CountHydrator extends AbstractHydrator
{
    const HYDRATOR_NAME = 'count';
    const FIELD = 'count';

    /**
     * {@inheritDoc}
     */
    protected function hydrateAllData()
    {
        return (int)$this->_stmt->fetchColumn(0);
    }
}