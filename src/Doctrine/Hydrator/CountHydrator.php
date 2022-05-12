<?php

namespace Hgabka\UtilsBundle\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class CountHydrator extends AbstractHydrator
{
    public const HYDRATOR_NAME = 'count';
    public const FIELD = 'count';

    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData()
    {
        return (int) $this->_stmt->fetchOne();
    }
}
