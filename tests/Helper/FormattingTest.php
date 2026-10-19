<?php

namespace Hgabka\UtilsBundle\Tests\Helper;

use Hgabka\UtilsBundle\Helper\Formatter;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Hgabka\UtilsBundle\Helper\Number\NumberToText;
use Hgabka\UtilsBundle\Helper\Number\RomanHelper;
use Hgabka\UtilsBundle\Helper\Number\Words;
use Hgabka\UtilsBundle\Helper\Slugifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FormattingTest extends TestCase
{
    private function formatter(): Formatter
    {
        return new Formatter($this->createStub(HgabkaUtils::class));
    }

    public function testNumberAndPriceFormatting(): void
    {
        $formatter = $this->formatter();

        $this->assertSame('1 234 567', $formatter->formatNumber(1234567));
        $this->assertSame('1 234,50', $formatter->formatNumber(1234.5, 2));
        $this->assertSame('1.234,50', $formatter->formatNumber(1234.5, 2, ',', '.'));
        $this->assertSame('12 000 Ft', $formatter->formatPrice(12000, 0, ',', ' ', 'Ft'));
        $this->assertSame('12 000', $formatter->formatPrice(12000));
    }

    public function testOrdinalNames(): void
    {
        $formatter = $this->formatter();

        $this->assertSame('első', $formatter->getSorszamnev(1));
        $this->assertSame('tizenhetedik', $formatter->getSorszamnev(17));
        $this->assertSame('', $formatter->getSorszamnev(99));
    }

    public function testHighlightText(): void
    {
        $formatter = $this->formatter();

        $this->assertSame('a <b>Foo</b> and <b>bar</b>', $formatter->highlightText('a Foo and bar', 'Foo bar', '<b>', '</b>'));
        $this->assertSame('nothing', $formatter->highlightText('nothing', '  ', '<b>', '</b>'));
    }

    public function testSlugifier(): void
    {
        $slugifier = new Slugifier();

        $this->assertSame('arvizturo-tukorfurogep', $slugifier->slugify('Árvíztűrő tükörfúrógép'));
        $this->assertSame('a-b', $slugifier->slugify('a/b'));
        $this->assertSame('x', $slugifier->slugify('???', 'x'));
        $this->assertSame('a.b', str_replace('-', '.', $slugifier->slugify('a b')));
    }

    #[DataProvider('romans')]
    public function testRomanHelper(string $roman, int $integer): void
    {
        $helper = new RomanHelper();

        $this->assertTrue($helper->validate($roman));
        $this->assertSame($integer, $helper->parse($roman));
        $this->assertSame($roman, $helper->convert($integer));
    }

    public static function romans(): array
    {
        return [['I', 1], ['IV', 4], ['IX', 9], ['XIV', 14], ['XL', 40], ['XCIX', 99], ['CDXLIV', 444], ['MCMXCIV', 1994], ['MMMCMXCIX', 3999]];
    }

    public function testRomanHelperRejectsInvalidNumerals(): void
    {
        $helper = new RomanHelper();

        $this->assertFalse($helper->validate('IIII'));
        $this->assertFalse($helper->parse('VX'));
        $this->assertSame(0, $helper->parse('N'));
        $this->assertSame(14, $helper->parse('xiv'));
    }

    public function testNumbersToWordsInEnglish(): void
    {
        $words = new Words();

        $this->assertSame('forty-two', $words->toWords(42, 'en_US'));
        $this->assertSame('one hundred twenty-three', $words->toWords(123, 'en_US'));
        $this->assertSame('one thousand', $words->toWords('1000', 'en_US'));
        $this->assertSame('minus five', $words->toWords(-5, 'en_US'));
    }

    public function testUnknownWordsLocaleFails(): void
    {
        $this->expectException(\Exception::class);
        (new Words())->toWords(1, 'xx');
    }

    public function testNumberToTextStripsTheMinusSign(): void
    {
        $this->assertSame('forty-two', (new NumberToText())->convert('-42', 'en_US'));
    }
}
