<?php

namespace Hgabka\UtilsBundle\Export\Traits;

use Hgabka\UtilsBundle\Export\ExportField;

trait CsvTrait
{
    protected $rows = [];

    /** @var string */
    protected $separator = ';';

    /** @var bool */
    protected $withBOM = false;

    protected $encoding;

    protected function init()
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

    protected function setCellValue($column, $value, ExportField $field = null)
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
