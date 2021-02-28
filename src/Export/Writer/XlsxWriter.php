<?php

namespace Hgabka\UtilsBundle\Export\Writer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxWriter implements TypedWriterInterface
{
    const LABEL_COLUMN = 1;
    /** @var int */
    protected $position;
    /** @var Spreadsheet */
    private $phpExcelObject;
    /** @var array */
    private $headerColumns = [];
    /** @var string */
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->position = 2;
    }

    public function getDefaultMimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function getFormat(): string
    {
        return 'xlsx';
    }

    /**
     * Create PHPExcel object and set defaults.
     */
    public function open()
    {
        $this->phpExcelObject = new Spreadsheet();
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $data)
    {
        $this->init($data);
        foreach ($data as $header => $value) {
            $cell = $this->getColumn($header);
            $column = $this->headerColumns[$header];
            if (\is_array($value)) {
                $this->setCellValue($cell, $value['value']);
                if (isset($value['format'])) {
                    $this->getActiveSheet()->getStyle($column . '2:' . $column . $this->position)->getNumberFormat()->setFormatCode($value['format']);
                }
                if (false !== strpos($value['value'], "\r") || false !== strpos($value['value'], "\n")) {
                    $this->getActiveSheet()->getStyle($column . '2:' . $column . $this->position)->getAlignment()->setWrapText(true);
                }
            } else {
                $this->setCellValue($cell, $value);
                if (false !== strpos($value, "\r") || false !== strpos($value, "\n")) {
                    $this->getActiveSheet()->getStyle($column . '2:' . $column . $this->position)->getAlignment()->setWrapText(true);
                }
            }
        }
        ++$this->position;
    }

    /**
     * Save Excel file.
     */
    public function close()
    {
        $this->phpExcelObject->getActiveSheet()->setSelectedCell('A1');
        $writer = new Xlsx($this->phpExcelObject);
        $writer->save($this->filename);
    }

    /**
     * Returns letter for number based on Excel columns.
     *
     * @param int $number
     *
     * @return string
     */
    public static function formatColumnName($number)
    {
        for ($char = ''; $number >= 0; $number = (int) ($number / 26) - 1) {
            $char = \chr($number % 26 + 0x41) . $char;
        }

        return $char;
    }

    /**
     *  Set labels.
     *
     * @param $data
     */
    protected function init($data)
    {
        if ($this->position > 2) {
            return;
        }
        $i = 0;
        foreach ($data as $header => $value) {
            $column = self::formatColumnName($i);
            $this->setHeader($column, $header);
            ++$i;
        }
        $this->setBoldLabels();
    }

    /**
     * @return Worksheet
     */
    private function getActiveSheet()
    {
        return $this->phpExcelObject->getActiveSheet();
    }

    /**
     * Makes header bold.
     */
    private function setBoldLabels()
    {
        $this->getActiveSheet()->getStyle(
            sprintf(
                '%s1:%s1',
                reset($this->headerColumns),
                end($this->headerColumns)
            )
        )->getFont()->setBold(true);
    }

    /**
     * Sets cell value.
     *
     * @param string $column
     * @param string $value
     */
    private function setCellValue($column, $value)
    {
        $this->getActiveSheet()->setCellValue($column, $value);
    }

    /**
     * Set column label and make column auto size.
     *
     * @param string $column
     * @param string $value
     */
    private function setHeader($column, $value)
    {
        $this->setCellValue($column . self::LABEL_COLUMN, $value);
        $this->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        $this->headerColumns[$value] = $column;
    }

    /**
     * Get column name.
     *
     * @param string $name
     *
     * @return string
     */
    private function getColumn($name)
    {
        return $this->headerColumns[$name] . $this->position;
    }
}
