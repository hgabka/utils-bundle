<?php

namespace Hgabka\UtilsBundle\Filter;

use function array_key_exists;
use Hgabka\UtilsBundle\Form\Type\NumberRangeType;
use function is_array;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class NumberRangeFilter extends Filter
{
    protected $range = true;

    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, string $alias, string $field, FilterData $data): void
    {
        // check data sanity

        if (!$data->hasValue() || !is_array($data->getValue())) {
            return;
        }

        if ($this->range) {
            $value = $data->getValue();
            // additional data check for ranged items
            if (!array_key_exists('start', $value) || !array_key_exists('end', $value)) {
                return;
            }
            $this->setActive(true);
            $hasStart = '' !== $value['start'] && null !== $value['start'];
            $hasEnd = '' !== $value['end'] && null !== $value['end'];

            if (!$hasStart && !$hasEnd) {
                return;
            }

            if ($hasStart) {
                $startQuantity = $this->getNewParameterName($query);
                $this->applyWhere($query, sprintf('%s.%s %s :%s', $alias, $field, '>=', $startQuantity));
                $query->setParameter($startQuantity, $value['start']);
            }

            if ($hasEnd) {
                $endQuantity = $this->getNewParameterName($query);
                $this->applyWhere($query, sprintf('%s.%s %s :%s', $alias, $field, '<=', $endQuantity));
                $query->setParameter($endQuantity, $value['end']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(): array
    {
        return [
            'operator_type' => HiddenType::class,
            'operator_options' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions(): array
    {
        return [
                'field_type' => NumberRangeType::class,
                'field_options' => $this->getOption('field_options', []),
                'operator_type' => $this->getOption('operator_type'),
                'operator_options' => $this->getOption('operator_options'),
                'label' => $this->getLabel(),
        ];
    }
}
