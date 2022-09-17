<?php

namespace Hgabka\UtilsBundle\Twig\Extension;

use Hgabka\UtilsBundle\Helper\Formatter;
use Hgabka\UtilsBundle\Helper\SlugifierInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UtilitiesTwigExtension extends AbstractExtension
{
    public function __construct(protected SlugifierInterface $slugifier, protected Formatter $formatter) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('slugify', [$this, 'slugify']),
            new TwigFilter('utils_format_number', [$this, 'formatNumber']),
            new TwigFilter('utils_format_price', [$this, 'formatPrice']),
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function slugify($text): ?string
    {
        return $this->slugifier->slugify($text, '');
    }

    public function formatNumber($number, int $decimals = 0, string $decimalSeparator = ',', string $thousandsSeparator = ' '): string
    {
        return $this->formatter->formatNumber($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    public function formatPrice($price, int $decimals = 0, string $decimalSeparator = ',', string $thousandsSeparator = ' ', ?string $withCurrency = null): string
    {
        return $this->formatter->formatPrice($price, $decimals, $decimalSeparator, $thousandsSeparator, $withCurrency);
    }
}
