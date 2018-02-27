<?php

namespace Hgabka\UtilsBundle\AdminList\FilterType\ORM;

use Symfony\Component\HttpFoundation\Request;

/**
 * EnumerationFilterType.
 */
class EnumerationFilterType extends AbstractORMFilterType
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_'.$uniqueId);
        $data['value'] = $request->query->get('filter_value_'.$uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            $colName = false === stripos($this->columnName, '.') ? $this->getAlias().$this->columnName : $this->columnName;

            switch ($data['comparator']) {
                case 'in':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->in($colName, ':var_'.$uniqueId));
                    $this->queryBuilder->setParameter('var_'.$uniqueId, $data['value'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);

                    break;
                case 'notin':
                    $this->queryBuilder->andWhere($this->queryBuilder->expr()->notIn($colName, ':var_'.$uniqueId));
                    $this->queryBuilder->setParameter('var_'.$uniqueId, $data['value'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);

                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@HgabkaUtils/FilterType/enumerationFilter.html.twig';
    }
}
