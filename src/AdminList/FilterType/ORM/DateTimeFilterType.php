<?php

namespace Hgabka\UtilsBundle\AdminList\FilterType\ORM;

use DateTime;
use Symfony\Component\HttpFoundation\Request;

/**
 * DateTimeFilterType.
 */
class DateTimeFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            /** @var DateTime $datetime */
            $dateTime = DateTime::createFromFormat('Y-m-d H:i', $data['value']);

            if (false === $dateTime) {
                // Failed to create DateTime object.
                return;
            }

            $colName = false === stripos($this->columnName, '.') ? $this->getAlias() . $this->columnName : $this->columnName;

            switch ($data['comparator']) {
                case 'before':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->lte($colName, ':var_' . $uniqueId));

                    break;
                case 'after':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->gt($colName, ':var_' . $uniqueId));

                    break;
            }
            $this->queryBuilder->setParameter('var_' . $uniqueId, $dateTime->format('Y-m-d H:i'));
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@HgabkaUtils/FilterType/dateTimeFilter.html.twig';
    }
}
