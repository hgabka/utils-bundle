<?php

namespace App\Export;

class ExportField
{
    /** @var string|null */
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

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return ExportField
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
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
