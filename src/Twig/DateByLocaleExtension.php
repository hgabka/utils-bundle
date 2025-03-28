<?php

namespace Hgabka\UtilsBundle\Twig;

use IntlDateFormatter as DateFormatter;
use Twig\Extension\AbstractExtension;

/**
 * DateByLocaleExtension.
 */
class DateByLocaleExtension extends AbstractExtension
{
    /**
     * Get Twig filters defined in this extension.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('localeDate', '\Hgabka\UtilsBundle\Twig\DateByLocaleExtension::localeDateFilter'),
        ];
    }

    /**
     * A date formatting filter for Twig, renders the date using the specified parameters.
     *
     * @param mixed  $date     Unix timestamp to format
     * @param string $locale   The locale
     * @param string $dateType The date type
     * @param string $timeType The time type
     * @param string $pattern  The pattern to use
     *
     * @return string
     */
    public static function localeDateFilter($date, $locale = 'hu', $dateType = 'medium', $timeType = 'none', ?string $pattern = null)
    {
        $values = [
            'none' => DateFormatter::NONE,
            'short' => DateFormatter::SHORT,
            'medium' => DateFormatter::MEDIUM,
            'long' => DateFormatter::LONG,
            'full' => DateFormatter::FULL,
        ];

        if (null === $pattern) {
            $dateFormatter = DateFormatter::create(
                $locale,
                $values[$dateType],
                $values[$timeType],
                'Europe/Brussels'
            );
        } else {
            $dateFormatter = DateFormatter::create(
                $locale,
                $values[$dateType],
                $values[$timeType],
                'Europe/Brussels',
                DateFormatter::GREGORIAN,
                $pattern
            );
        }

        return $dateFormatter->format($date);
    }
}
