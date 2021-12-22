<?php

namespace Hgabka\UtilsBundle\Export;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportFieldDescriptor
{
    /** @var array */
    protected $fields = [];

    /** @var TranslatorInterface */
    protected $translator;

    /** @var HgabkaUtils */
    protected $hgabkaUtils;

    protected $translateLabels = true;

    /**
     * ExportFieldDescriptor constructor.
     */
    public function __construct(TranslatorInterface $translator, HgabkaUtils $hgabkaUtils)
    {
        $this->translator = $translator;
        $this->hgabkaUtils = $hgabkaUtils;
    }

    /**
     * @param bool $translateLabels
     * @return ExportFieldDescriptor
     */
    public function setTranslateLabels(bool $translateLabels): ExportFieldDescriptor
    {
        $this->translateLabels = $translateLabels;

        return $this;
    }


    /**
     * @param       $key
     * @param null  $type
     * @param array $options
     *
     * @return $this
     */
    public function add($key, $type = null, $options = []): self
    {
        if (!isset($options['label'])) {
            $options['label'] = 'label.export.' . str_replace('.', '_', $key);
        }

        if ($this->translateLabels) {
            if (!isset($options['translate_label']) || false !== $options['translate_label']) {
                $options['label'] = $this->translator->trans($options['label'], [], 'messages');
            }
        } else {
            if (true === ($options['translate_label'] ?? false)) {
                $options['label'] = $this->translator->trans($options['label'], [], 'messages');
            }
        }

        if (!isset($options['key'])) {
            $options['key'] = $key;
        }

        $this->fields[$key] = new ExportField($type ?? 'auto', $options);

        return $this;
    }

    /**
     * @param $key
     */
    public function remove($key): void
    {
        unset($this->fields[$key]);
    }

    /**
     * @param $key
     *
     * @return null|mixed
     */
    public function get($key)
    {
        if (empty($this->fields[$key])) {
            throw new InvalidArgumentException('Invalid export key: ' . $key);
        }

        return $this->fields[$key] ?? null;
    }

    /**
     * @param $key
     * @param $option
     * @param $value
     */
    public function setFieldOption($key, $option, $value): void
    {
        $field = $this->get($key);

        $field->setOption($option, $value);
    }

    /**
     * @param $key
     * @param $label
     */
    public function setLabel($key, $label): void
    {
        $this->setFieldOption($key, 'label', $label);
    }

    /**
     * @param $keys
     */
    public function reorder($keys): void
    {
        $newFields = [];
        foreach ($keys as $key) {
            $newFields[$key] = $this->get($key);
        }

        $this->fields = $newFields;
    }

    public function all(): array
    {
        return $this->fields;
    }

    public function clear(): void
    {
        $this->fields = [];
    }

    public function getSchemeAndHttpHost(): string
    {
        return $this->hgabkaUtils->getSchemeAndHttpHost();
    }
}
