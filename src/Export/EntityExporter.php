<?php

namespace Hgabka\UtilsBundle\Export;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

abstract class EntityExporter
{
    /** @var null string */
    protected $encoding = null;

    /**
     * @var array
     */
    protected $headers;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ExportFieldDescriptor */
    protected $fieldDescriptor;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @var int
     */
    protected $currentRow = 1;

    /**
     * @return Generator
     */
    abstract public function getData(): Generator;

    /**
     * @param $object
     * @param $field
     *
     * @return mixed|null
     */
    protected function getObjectFieldValue($object, $field)
    {
        $field = ucfirst(Container::camelize($field));

        $accessor = PropertyAccess::createPropertyAccessor();

        try {
            return $accessor->getValue($object, $field);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @param        $id
     * @param array  $params
     * @param string $domain
     *
     * @return string
     */
    protected function trans($id, $params = [], $domain = 'messages')
    {
        return $this->translator->trans($id, $params, $domain, 'hu');
    }

    /**
     * @param        $id
     * @param        $number
     * @param array  $params
     * @param string $domain
     *
     * @return mixed
     */
    protected function transChoice($id, $number, $params = [], $domain = 'messages')
    {
        return $this->translator->transChoice($id, $number, $params, $domain, 'hu');
    }

    /**
     * @return array|string[]
     */
    protected function getHeaders()
    {
        if (empty($this->headers)) {
            $this->headers = [];
            foreach ($this->fieldDescriptor->all() as $key => $field) {
                $this->headers[] = [$key => $field->getLabel()];
            }
        }

        return $this->headers;
    }

    /**
     * @param array $fields
     */
    protected function postWriteData(array $fields)
    {
    }

    /**
     * Set column label and make column auto size.
     *
     * @param string $column
     * @param string $value
     */
    protected function setHeader(string &$column, $value)
    {
        $this->addCellValue($column, $value);
    }

    /**
     * @param                  $column
     * @param                  $value
     * @param ExportField|null $field
     *
     * @return mixed
     */
    abstract protected function setCellValue($column, $value, ?ExportField $field = null);

    /**
     * @param $filename
     *
     * @return mixed
     */
    abstract protected function saveContent(?string $filename): void;

    /**
     * @return mixed
     */
    abstract protected function init();

    protected function addFields()
    {
    }

    protected function writeHeader()
    {
        $i = 0;
        foreach ($this->getHeaders() as $key) {
            if (is_array($key)) {
                $this->setHeader($i, current($key));
            } else {
                $field = $this->fieldDescriptor->get($key);
                $this->setHeader($i, $field->getOptions()['label']);
            }
        }

        $this->currentRow = 2;
    }

    /**
     * @param $object
     * @param $field
     *
     * @return mixed|string|null
     */
    protected function getRelationValue($object, $field): ?string
    {
        $parts = explode('.', $field, 2);
        $relation = $this->getEntityFieldValue($object, $parts[0]);
        if (!$relation) {
            return '';
        }

        if (!is_object($relation) || count($parts) <= 1) {
            return (string) $relation;
        }

        return false === strpos($parts[1], '.')
            ? $this->getObjectFieldValue($relation, $parts[1])
            : $this->getRelationValue($relation, $parts[1]);
    }

    /**
     * @param        $row
     * @param object $entity
     */
    protected function preWriteRow($row, object $entity): bool
    {
        return true;
    }

    /**
     * @param        $row
     * @param object $entity
     */
    protected function postWriteRow($row, object $entity)
    {
    }

    /**
     * @param object $entity
     * @param        $field
     *
     * @return mixed|null
     */
    protected function getEntityFieldValue(object $entity, $field)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());

        $field = $inflector->tableize($field);

        return $this->getObjectFieldValue($entity, $field);
    }

    /**
     * @param             $column
     * @param ExportField $field
     * @param object      $entity
     * @param int         $row
     */
    protected function writeColumn(&$column, ExportField $field, object $entity, int $row)
    {
        $options = $field->getOptions();

        if (array_key_exists('callback', $options)) {
            $value = is_callable($options['callback']) ? call_user_func($options['callback'], $entity, $column, $field, $row) : '';
            $this->addCellValue($column, (string) $value, $entity, $options['value_callback'] ?? null, $field, $row);

            return;
        }

        if (!isset($options['value'])) {
            if (isset($options['property_path'])) {
                $this->addCellValue($column, $this->getEntityFieldValue($entity, $options['property_path']), $entity, $options['value_callback'] ?? null, $field, $row);
            } else {
                $method = 'addCellValue' . ucfirst($field->getType());
                if (!method_exists($this, $method)) {
                    throw new InvalidArgumentException('Nincs ilyen metodus: ', $method);
                }

                $this->$method($column, $options['key'], $entity, $options['value_callback'] ?? null, $field, $row);
            }
        } else {
            $this->addCellValue($column, (string) $options['value'], $entity, $options['value_callback'] ?? null, $field, $row);
        }
    }

    /**
     * @param                  $column
     * @param                  $value
     * @param object|null      $entity
     * @param callable|null    $callback
     * @param ExportField|null $field
     */
    protected function addCellValue(&$column, $value, ?object $entity = null, ?callable $callback = null, ?ExportField $field = null, ?int $row = null)
    {
        $value = is_callable($callback) ? $callback($value, $entity, $row) : $value;
        if ((null === $field || false !== $field->getOption('trim')) && is_string($value)) {
            $value = trim($value);
        }

        $value = $this->isUtf8() || !is_string($value)
            ? $value
            : mb_convert_encoding($value, $this->encoding, 'UTF-8')
        ;
        if (is_string($value)) {
            $value = str_replace('%%row%%', $row, $value);
        }

        $this->setCellValue($column++, $value, $field);
    }

    /**
     * @param                  $column
     * @param                  $field
     * @param object|null      $entity
     * @param callable|null    $callback
     * @param ExportField|null $exportField
     */
    protected function addCellValueAuto(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null)
    {
        if (false !== strpos($field, '.')) {
            $this->addCellValueRelation($column, $field, $entity, $callback, $exportField, $row);
        } else {
            $this->addCellValue($column, $this->getEntityFieldValue($entity, $field), $entity, $callback, $exportField, $row);
        }
    }

    /**
     * @param                  $column
     * @param                  $field
     * @param object|null      $entity
     * @param callable|null    $callback
     * @param ExportField|null $exportField
     * @param int|null         $row
     */
    protected function addCellValueBool(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null)
    {
        $this->addCellValueAuto($column, $field, $entity, null === $callback ? function ($value) {
            return $this->trans('general.label.' . ($value ? 'yes' : 'no'));
        } : $callback, $exportField, $row);
    }

    /**
     * @param                  $column
     * @param                  $field
     * @param object|null      $entity
     * @param callable|null    $callback
     * @param ExportField|null $exportField
     * @param int|null         $row
     */
    protected function addCellValueRelation(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null)
    {
        $this->addCellValue($column, $this->getRelationValue($entity, $field), $entity, $callback, $exportField, $row);
    }

    protected function writeData()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $row = 1;
        foreach ($this->getData() as $entity) {
            if (false === $this->preWriteRow($this->currentRow, $entity)) {
                continue;
            }
            
            $i = 0;
            foreach ($this->getHeaders() as $key) {
                if (is_array($key)) {
                    $key = key($key);
                }
                $field = $this->fieldDescriptor->get($key);
                $this->writeColumn($i, $field, $entity, $row);
            }

            $this->postWriteRow($this->currentRow, $entity);
            $this->entityManager->clear($this->getClass());
            ++$this->currentRow;
            ++$row;
        }

        $this->postWriteData($this->getHeaders());
    }

    protected function write()
    {
        $this->addFields();

        $this->writeHeader();
        $this->writeData();
    }

    /**
     * @return bool
     */
    protected function isUtf8(): bool
    {
        return null === $this->encoding || in_array(strtolower($this->encoding), ['utf-8', 'utf8']);
    }
}
