<?php

namespace Hgabka\UtilsBundle\Export\Traits;

use Hgabka\UtilsBundle\Export\ExportField;
use Hgabka\UtilsBundle\Export\Writer\XlsxWriter;
use function is_array;
use function is_string;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ExcelTrait
{
    /** @var string */
    protected $workSheetName;

    /** @var Spreadsheet */
    protected $spreadsheet;

    /** @var null|array|string */
    protected $autoSizeColumns = true;

    /**
     * @return string
     */
    public function getWorkSheetName(): string
    {
        return $this->workSheetName;
    }

    /**
     * @param string $workSheetName
     *
     * @return self
     */
    public function setWorkSheetName(string $workSheetName): self
    {
        if ($workSheetName) {
            $workSheetName = str_replace('/', '|', mb_substr($workSheetName, 0, 31));
        }

        $this->workSheetName = $workSheetName;

        return $this;
    }

    protected function init()
    {
        $this->spreadsheet = new Spreadsheet();
        $this
            ->spreadsheet
            ->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(10)
            ->getColor()
            ->setRGB('000000')
        ;
    }

    /**
     * Save Excel file.
     *
     * @param $filename
     *
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function saveContent(?string $filename): void
    {
        $this->spreadsheet->getActiveSheet()->setSelectedCell('A1')
        ;
        $writer = new Xlsx($this->spreadsheet);

        $writer->save($filename);
    }

    protected function postWriteData(array $fields)
    {
        if (!empty($this->getWorkSheetName())) {
            $this->getActiveSheet()->setTitle($this->getWorkSheetName())
            ;
        }

        $i = 0;
        $noAutoSizeColumns = [];
        $colWidths = [];
        foreach ($fields as $key) {
            if (is_array($key)) {
                $key = key($key);
            }

            $field = $this->fieldDescriptor->get($key);
            $options = $field->getOptions();
            if (isset($options['format'])) {
                $column = XlsxWriter::formatColumnName($i);
                $this->getActiveSheet()->getStyle($column . '2:' . $column . $this->currentRow)->getNumberFormat()->setFormatCode($options['format'])
                ;
            }

            if (isset($options['autosize']) && false === $options['autosize']) {
                $noAutoSizeColumns[] = $i + 1;
            }

            if (isset($options['width'])) {
                $colWidths[$i + 1] = $options['width'];
            }
            ++$i;
        }
        $this->setHeaderStyle();

        if (null !== $this->autoSizeColumns || !empty($colWidths)) {
            Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_APPROX);
            $colIterator = $this->getActiveSheet()->getColumnIterator();
            foreach ($colIterator as $col) {
                $numIndex = Coordinate::columnIndexFromString($col->getColumnIndex());
                if (!empty($colWidths[$numIndex])) {
                    $this->getActiveSheet()->getColumnDimension($col->getColumnIndex())->setAutoSize(false);
                    $this->getActiveSheet()->getColumnDimension($col->getColumnIndex())->setWidth($colWidths[$numIndex]);

                    continue;
                }
                if (in_array($numIndex, $noAutoSizeColumns, true) || null === $this->autoSizeColumns) {
                    continue;
                }
                if (true === $this->autoSizeColumns ||
                    (is_string($this->autoSizeColumns) && $col->getColumnIndex() === $this->autoSizeColumns) ||
                    (is_array($this->autoSizeColumns) && in_array($col->getColumnIndex(), $this->autoSizeColumns, true))) {
                    $this->getActiveSheet()->getColumnDimension($col->getColumnIndex())->setAutoSize(true);
                }
            }
        }
    }

    protected function getCellName($column, $row)
    {
        return $column = XlsxWriter::formatColumnName($column) . $row;
    }

    /**
     * Set column label and make column auto size.
     *
     * @param string $column
     * @param string $value
     *
     * @throws Exception
     */
    protected function setHeader(&$column, $value)
    {
        parent::setHeader($column, $value);

        $thisCol = XlsxWriter::formatColumnName($column);
        $this->getActiveSheet()->getColumnDimension($thisCol)->setAutoSize(true)
        ;
    }

    /**
     * @return Worksheet
     */
    protected function getActiveSheet(): Worksheet
    {
        return $this->spreadsheet->getActiveSheet();
    }

    /**
     * Sets cell value.
     *
     * @param mixed|string     $column
     * @param string           $value
     * @param null|ExportField $field
     *
     * @throws Exception
     */
    protected function setCellValue($column, $value, ?ExportField $field = null)
    {
        $cell = XlsxWriter::formatColumnName($column) . ($this->currentRow);
        $this->getActiveSheet()->setCellValue($cell, $value)
        ;
        $align = $this->getActiveSheet()->getStyle($cell)->getAlignment();
        if (false !== strpos($value, "\n")) {
            $align->setWrapText(true);
        }
        if ($field && $field->getOption('vertical_align')) {
            $align->setVertical($field->getOption('vertical_align'));
        } else {
            $align->setVertical(Alignment::VERTICAL_TOP);
        }

        if ($field && $field->getOption('align')) {
            $align->setHorizontal($field->getOption('align'));
        }
    }

    /**
     * Makes header bold.
     */
    protected function setHeaderStyle()
    {
        $style = $this->getActiveSheet()->getStyle(
            sprintf(
                '%s1:%s1',
                XlsxWriter::formatColumnName(0),
                XlsxWriter::formatColumnName(count($this->headers) - 1)
            )
        );

        $style->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'dddddd'],
            ],
        ]);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)
        ;
        $style->getFont()->setBold(true)
        ;
        $this->getActiveSheet()->getRowDimension('1')->setRowHeight(30)
        ;
    }

    protected function getMimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }
}
