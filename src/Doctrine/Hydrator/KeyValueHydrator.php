<?php

namespace Hgabka\UtilsBundle\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class KeyValueHydrator extends AbstractHydrator
{
    public const HYDRATOR_NAME = 'key_value';

    /**
     * Hydrators all data at once.
     *
     * @return array
     */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAllKeyValue();
    }

    /**
     * Hydrates a row.
     *
     * @SuppressWarnings(PMD.ElseExpression)
     *
     * @return bool
     */
    protected function hydrateRowData(array $row, array & $result)
    {
        if (0 === \count($row)) {
            return false;
        }

        $keys = array_keys($row);

        $id = $row[$keys[0]];

        if (2 === \count($row)) {
            $value = $row[$keys[1]];
        } else {
            array_shift($row);
            $value = $row;
        }

        $result[$id] = $value;

        return true;
    }
}
