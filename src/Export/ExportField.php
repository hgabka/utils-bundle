<?php

namespace Hgabka\UtilsBundle\Export;

class ExportField
{
    /** @var null|string */
    protected $type;

    /** @var array */
    protected $options = [];

    /**
     * ExportField constructor.
     *
     * @param $type
     * @param $options
     */
    public function __construct(?string $type = null, array $options = [])
    {
        $this->type = $type;
        $this->options = $options;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return ExportField
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return ExportField
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOption($key)
    {
        return $this->options[$key] ?? null;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function getLabel()
    {
        return $this->getOption('label');
    }
}
