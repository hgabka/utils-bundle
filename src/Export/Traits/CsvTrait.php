<?php

namespace Hgabka\UtilsBundle\Export\Traits;

use Hgabka\UtilsBundle\Export\ExportField;

trait CsvTrait
{
    protected array $rows = [];

    /** @var string */
    protected string $separator = ';';

    /** @var bool */
    protected bool $withBOM = false;

    protected ?string $encoding = null;

    protected function init(): void
    {
    }

    /**
     * Save Excel file.
     *
     * @param $filename
     */
    protected function saveContent(?string $filename): void
    {
        $fp = fopen($filename, 'w');
        if ($this->withBOM) {
            fwrite($fp, "\xEF\xBB\xBF");
        }
        foreach ($this->rows as $row => $data) {
            fputcsv($fp, $data, $this->separator, '"');
        }

        fclose($fp);
    }

    protected function setCellValue($column, $value, ExportField $field = null): void
    {
        if (!array_key_exists($this->currentRow, $this->rows)) {
            $this->rows[$this->currentRow] = [];
        }

        $this->rows[$this->currentRow][$column] = stripslashes(str_replace(';', '', $value));
    }

    protected function getMimeType(): string
    {
        return 'text/csv';
    }

    protected function sanitizeForCsv(string $value): string
    {
        return stripslashes(str_replace(';', '', $value));
    }
}
