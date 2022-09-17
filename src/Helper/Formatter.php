<?php

namespace Hgabka\UtilsBundle\Helper;

use IntlDateFormatter;
use IntlTimeZone;

class Formatter
{
    public function __construct(protected HgabkaUtils $utils) {}

    public function formatPrice($price, int $decimals = 0, string $decimalSeparator = ',', string $thousandsSeparator = ' ', ?string $withCurrency = null): string
    {
        return $this->formatNumber($price, $decimals, $decimalSeparator, $thousandsSeparator) . ($withCurrency ? ' ' . $withCurrency : '');
    }

    public function formatNumber($number, int $decimals = 0, string $decimalSeparator = ',', string $thousandsSeparator = ' '): string
    {
        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }
    
    public function getSorszamnev(int $szam): string
    {
        $sorszamok = [
            'nulladik',
            'első',
            'második',
            'harmadik',
            'negyedik',
            'ötödik',
            'hatodik',
            'hetedik',
            'nyolcadik',
            'kilencedik',
            'tizedik',
            'tizenegyedik',
            'tizenkettedik',
            'tizenharmadik',
            'tizennegyedik',
            'tizenötödik',
            'tizenhatodik',
            'tizenhetedik',
        ];

        return $sorszamok[$szam] ?? '';
    }

    public function getRag(int $szam): string
    {
        $utso = $szam[strlen($szam) - 1];

        switch ($utso) {
            case 0:
            case 1:
            case 2:
            case 4:
            case 5:
            case 9:
            case 7:
                return 'ben';
            default:
                return 'ban';
        }
    }

    /**
     * @param null|string $text
     *
     * @return string
     */
    public function convertToMetaText(?string $text): string
    {
        return $this->utils->convertToMetaText($text);
    }

    /**
     * @param string $text
     * @param int    $max
     * @param bool   $addEllipsis
     *
     * @return string
     */
    public function shortenText(string $text, int $max = 100, bool $addEllipsis = false): string
    {
        return $this->utils->shortenText($text, $max, $addEllipsis);
    }

    /**
     * @param string $text
     * @param int    $length
     *
     * @return string
     */
    public function truncateToNearestWord(string $text, int $length): string
    {
        return $this->utils->truncateToNearestWord($text, $length);
    }

    public function highlightText(string $text, string $textToHighlight, string $prefix = '<span style="font-weight: bold !important;">', string $postfix = '</span>'): string
    {
        $searchArray = explode(' ', $textToHighlight);
        $searches = [];
        $replacements = [];
        foreach ($searchArray as $search) {
            if (strlen(trim($search))) {
                $searches[] = $search;
                $replacements[] = '![[' . $search . ']]!';
            }
        }

        return strtr(str_ireplace($searches, $replacements, $text), ['![[' => $prefix, ']]!' => $postfix]);
    }

    public function formatDate(DateTimeInterface $date, string $dateFormat = 'full', string $timeFormat = 'none', ?string $format = 'yyyy. MMMM d.'): string
    {
        $formatValues = [
            'none' => IntlDateFormatter::NONE,
            'short' => IntlDateFormatter::SHORT,
            'medium' => IntlDateFormatter::MEDIUM,
            'long' => IntlDateFormatter::LONG,
            'full' => IntlDateFormatter::FULL,
        ];

        $formatter = IntlDateFormatter::create(
            null,
            $formatValues[$dateFormat],
            $formatValues[$timeFormat],
            IntlTimeZone::createTimeZone($date->getTimezone()->getName()),
            IntlDateFormatter::GREGORIAN,
            $format
        );

        return $formatter->format($date->getTimestamp());
    }
}
