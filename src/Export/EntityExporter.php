<?php

namespace Hgabka\UtilsBundle\Export;

use DateTimeInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

abstract class EntityExporter
{
    protected ?string $encoding = null;

    protected ?array $headers = null;

    protected EntityManagerInterface $entityManager;

    protected ExportFieldDescriptor $fieldDescriptor;

    protected TranslatorInterface $translator;

    protected int $currentRow = 1;

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    #[Required]
    public function setFieldDescriptor(ExportFieldDescriptor $fieldDescriptor): self
    {
        $this->fieldDescriptor = $fieldDescriptor;

        return $this;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    abstract public function getData(): Generator;

    /**
     * @param $filename
     */
    public function save(string $filename): void
    {
        $this->write();
        $this->saveContent($filename);
    }

    public function getStreamedResponse(string $filename): StreamedResponse
    {
        return new StreamedResponse(function () {
            $this->save('php://output');
        }, 200, [
            'Content-Type' => $this->getMimeType(),
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
    }

    protected function getObjectFieldValue(object $object, string $field): mixed
    {
        $field = ucfirst(Container::camelize($field));

        $accessor = PropertyAccess::createPropertyAccessor();

        try {
            return $accessor->getValue($object, $field);
        } catch (Throwable $e) {
            return null;
        }
    }

    protected function trans(string $id, array $params = [], string $domain = 'messages'): string
    {
        return $this->translator->trans($id, $params, $domain, 'hu');
    }

    /**
     * @return array|string[]
     */
    protected function getHeaders(): ?array
    {
        if (empty($this->headers)) {
            $this->headers = [];
            foreach ($this->fieldDescriptor->all() as $key => $field) {
                $this->headers[] = [$key => $field->getLabel()];
            }
        }

        return $this->headers;
    }

    protected function postWriteData(array $fields): void
    {
    }

    /**
     * Set column label and make column auto size.
     *
     * @param string $value
     */
    protected function setHeader(string &$column, string $value)
    {
        $this->addCellValue($column, $value);
    }

    abstract protected function setCellValue($column, string $value, ?ExportField $field = null): void;

    abstract protected function saveContent(?string $filename): void;

    abstract protected function getMimeType(): string;

    abstract protected function init(): void;

    protected function addFields(): void
    {
    }

    protected function writeHeader(): void
    {
        $i = 0;
        foreach ($this->getHeaders() as $key) {
            if (\is_array($key)) {
                $this->setHeader($i, current($key));
            } else {
                $field = $this->fieldDescriptor->get($key);
                $this->setHeader($i, $field->getOptions()['label']);
            }
        }

        $this->currentRow = 2;
    }

    protected function getRelationValue(object $object, string $field): mixed
    {
        $parts = explode('.', $field, 2);
        $relation = $this->getEntityFieldValue($object, $parts[0]);
        if (!$relation) {
            return '';
        }

        if (!\is_object($relation) || \count($parts) <= 1) {
            return (string) $relation;
        }

        return false === strpos($parts[1], '.')
            ? $this->getObjectFieldValue($relation, $parts[1])
            : $this->getRelationValue($relation, $parts[1]);
    }

    protected function preWriteRow(int $row, object $entity): bool
    {
        return true;
    }

    /**
     * @param $row
     */
    protected function postWriteRow($row, object $entity): void
    {
    }

    protected function getEntityFieldValue(object $entity, string $field): mixed
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());

        $field = $inflector->tableize($field);

        return $this->getObjectFieldValue($entity, $field);
    }

    protected function writeColumn(&$column, ExportField $field, object $entity, int $row): void
    {
        $options = $field->getOptions();

        if (\array_key_exists('callback', $options)) {
            $value = \is_callable($options['callback']) ? \call_user_func($options['callback'], $entity, $column, $field, $row) : '';
            $this->addCellValue($column, $value, $entity, $options['value_callback'] ?? null, $field, $row);

            return;
        }

        if (!isset($options['value'])) {
            if (isset($options['property_path'])) {
                $this->addCellValue($column, $this->getEntityFieldValue($entity, $options['property_path']), $entity, $options['value_callback'] ?? null, $field, $row);
            } else {
                $method = 'addCellValue' . ucfirst($field->getType());
                if (!method_exists($this, $method)) {
                    throw new InvalidArgumentException('Nincs ilyen metodus: ' . $method);
                }

                $this->$method($column, $options['key'], $entity, $options['value_callback'] ?? null, $field, $row);
            }
        } else {
            $this->addCellValue($column, $options['value'], $entity, $options['value_callback'] ?? null, $field, $row);
        }
    }

    protected function addCellValue(&$column, $value, ?object $entity = null, ?callable $callback = null, ?ExportField $field = null, ?int $row = null): void
    {
        $value = \is_callable($callback) ? $callback($value, $entity, $row) : $value;
        if ((null === $field || false !== $field->getOption('trim')) && \is_string($value)) {
            $value = trim($value);
        }

        $value = $this->isUtf8() || !\is_string($value)
            ? $value
            : mb_convert_encoding($value, $this->encoding, 'UTF-8')
        ;
        if (\is_string($value)) {
            $value = str_replace('%%row%%', $row, $value);
        }

        $this->setCellValue($column++, $value, $field);
    }

    protected function addCellValueDate(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null): void
    {
        $dateValue = $this->getEntityFieldValue($entity, $field);
        if ($dateValue instanceof DateTimeInterface) {
            $options = $exportField ? $exportField->getOptions() : [];
            $dateValue = $dateValue->format($options['format'] ?? 'Y-m-d H:i:s');
        }

        $this->addCellValue($column, $dateValue, $entity, $callback, $exportField, $row);
    }

    protected function addCellValueAuto(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null): void
    {
        if (false !== strpos($field, '.')) {
            $this->addCellValueRelation($column, $field, $entity, $callback, $exportField, $row);
        } else {
            $this->addCellValue($column, $this->getEntityFieldValue($entity, $field), $entity, $callback, $exportField, $row);
        }
    }

    protected function addCellValueBool(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null): void
    {
        $this->addCellValueAuto($column, $field, $entity, null === $callback ? function ($value) {
            return $this->trans('general.label.' . ($value ? 'yes' : 'no'));
        } : $callback, $exportField, $row);
    }

    protected function addCellValueRelation(&$column, $field, ?object $entity = null, ?callable $callback = null, ?ExportField $exportField = null, ?int $row = null): void
    {
        $this->addCellValue($column, $this->getRelationValue($entity, $field), $entity, $callback, $exportField, $row);
    }

    protected function writeData(): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $row = 1;
        foreach ($this->getData() as $entity) {
            try {
                if (false === $this->preWriteRow($this->currentRow, $entity)) {
                    continue;
                }
            } catch (StopExportException $e) {
                $this->entityManager->clear($this->getClass());

                break;
            }

            $i = 0;
            foreach ($this->getHeaders() as $key) {
                if (\is_array($key)) {
                    $key = key($key);
                }
                $field = $this->fieldDescriptor->get($key);
                $this->writeColumn($i, $field, $entity, $row);
            }

            try {
                $this->postWriteRow($this->currentRow, $entity);
            } catch (StopExportException $e) {
                $this->entityManager->clear($this->getClass());

                break;
            }

            $this->entityManager->clear($this->getClass());
            ++$this->currentRow;
            ++$row;
        }

        $this->postWriteData($this->getHeaders());
    }

    protected function write(): void
    {
        $this->init();
        $this->addFields();

        $this->writeHeader();
        $this->writeData();
    }

    protected function isUtf8(): bool
    {
        return null === $this->encoding || \in_array(strtolower($this->encoding), ['utf-8', 'utf8'], true);
    }

    protected function createResponse(
        string $tmpName,
        string $disposition,
        string $filename,
    ): Response
    {
        $this->saveContent($tmpName);

        $content = file_get_contents($tmpName);
        $response = new Response();
        $response->setContent($content);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $this->getMimeType());
        $response->headers->set('Content-length', strlen($content));

        return $response;
    }
}
