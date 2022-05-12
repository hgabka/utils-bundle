<?php

namespace Hgabka\UtilsBundle\AdminList\FilterType\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * BooleanFilterType.
 */
class BooleanFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value'])) {
            $colName = false === stripos($this->columnName, '.') ? $this->getAlias() . $this->columnName : $this->columnName;
            switch ($data['value']) {
                case 'true':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($colName, 'true'));

                    break;
                case 'false':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->eq($colName, 'false'));

                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@HgabkaUtils/FilterType/booleanFilter.html.twig';
    }
}
