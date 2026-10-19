<?php

namespace Hgabka\UtilsBundle\Tests\Export;

use Hgabka\UtilsBundle\Export\Writer\CsvWriter;
use Hgabka\UtilsBundle\Export\Writer\XlsxWriter;
use Hgabka\UtilsBundle\Export\Writer\XmlWriter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WritersTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = sys_get_temp_dir() . '/hg_export_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
    }

    public function testCsvWriterWritesHeadersAndRows(): void
    {
        $writer = new CsvWriter($this->file);

        $writer->open();
        $writer->write(['name' => 'Ann', 'note' => 'says "hi"']);
        $writer->write(['name' => 'Béla', 'note' => 'a,b']);
        $writer->close();

        $this->assertSame("name,note\nAnn,\"says \"\"hi\"\"\"\nBéla,\"a,b\"\n", file_get_contents($this->file));
    }

    public function testCsvWriterOptions(): void
    {
        $writer = new CsvWriter($this->file, ';', "'", '\\', false, true);

        $writer->open();
        $writer->write(['a' => 'x y', 'b' => 'it;s']);
        $writer->close();

        $this->assertSame("\xEF\xBB\xBF'x y';'it;s'\n", file_get_contents($this->file));
    }

    public function testCsvWriterRefusesToOverwrite(): void
    {
        file_put_contents($this->file, 'existing');

        $this->expectException(\RuntimeException::class);
        new CsvWriter($this->file);
    }

    public function testCsvMetadata(): void
    {
        $writer = new CsvWriter($this->file);

        $this->assertSame('text/csv', $writer->getDefaultMimeType());
        $this->assertSame('csv', $writer->getFormat());
    }

    public function testXlsxWriterRoundTrip(): void
    {
        $writer = new XlsxWriter($this->file);

        $writer->open();
        $writer->write(['Name' => 'Ann', 'Age' => 30, 'Notes' => "line1\nline2"]);
        $writer->write(['Name' => 'Béla', 'Age' => ['value' => 41, 'format' => '0.00'], 'Notes' => '']);
        $writer->close();

        $sheet = IOFactory::load($this->file)->getActiveSheet();
        $this->assertSame(['Name', 'Age', 'Notes'], [$sheet->getCell('A1')->getValue(), $sheet->getCell('B1')->getValue(), $sheet->getCell('C1')->getValue()]);
        $this->assertTrue($sheet->getStyle('A1')->getFont()->getBold());
        $this->assertSame('Ann', $sheet->getCell('A2')->getValue());
        $this->assertEquals(30, $sheet->getCell('B2')->getValue());
        $this->assertSame('Béla', $sheet->getCell('A3')->getValue());
        $this->assertEquals(41, $sheet->getCell('B3')->getValue());
        $this->assertSame('0.00', $sheet->getStyle('B3')->getNumberFormat()->getFormatCode());
        $this->assertTrue($sheet->getStyle('C2')->getAlignment()->getWrapText());
    }

    public function testXlsxMetadata(): void
    {
        $writer = new XlsxWriter($this->file);

        $this->assertSame('xlsx', $writer->getFormat());
        $this->assertStringContainsString('spreadsheetml.sheet', $writer->getDefaultMimeType());
    }

    #[DataProvider('columns')]
    public function testXlsxColumnNames(int $index, string $name): void
    {
        $this->assertSame($name, XlsxWriter::formatColumnName($index));
    }

    public static function columns(): array
    {
        return [[0, 'A'], [1, 'B'], [25, 'Z'], [26, 'AA'], [27, 'AB'], [51, 'AZ'], [52, 'BA'], [701, 'ZZ'], [702, 'AAA']];
    }

    public function testXmlWriterWithCdata(): void
    {
        $writer = new XmlWriter($this->file, 'people', 'person');

        $writer->open();
        $writer->write(['name' => 'Ann & Co', 'address' => ['city' => 'Pécs', 'geo' => ['lat' => '1']]]);
        $writer->close();

        $xml = simplexml_load_file($this->file);
        $this->assertSame('people', $xml->getName());
        $this->assertSame('Ann & Co', (string) $xml->person->name);
        $this->assertSame('Pécs', (string) $xml->person->address->city);
        $this->assertSame('1', (string) $xml->person->address->geo->lat);
        $this->assertStringContainsString('<![CDATA[Ann & Co]]>', file_get_contents($this->file));
    }

    public function testXmlWriterWithoutCdataEscapesValues(): void
    {
        $writer = new XmlWriter($this->file, 'people', 'person', false);

        $writer->open();
        $writer->write(['name' => 'Ann & <Co>']);
        $writer->close();

        $this->assertSame('Ann & <Co>', (string) simplexml_load_file($this->file)->person->name);
        $this->assertStringNotContainsString('CDATA', file_get_contents($this->file));
    }

    public function testXmlMetadata(): void
    {
        $writer = new XmlWriter($this->file, 'r', 'x');

        $this->assertSame(['application/xml', 'xml'], [$writer->getDefaultMimeType(), $writer->getFormat()]);
    }
}
