<?php

namespace Hgabka\UtilsBundle\Helper;

use Behat\Transliterator\Transliterator;

/**
 * Slugifier is a helper to slugify a certain string.
 */
class Slugifier implements SlugifierInterface
{
    public function slugify($text, $default = '', $replace = ["'"], $delimiter = '-')
    {
        if (!empty($replace)) {
            $text = str_replace($replace, ' ', $text);
        }
        $text =  Transliterator::transliterate($text, $delimiter);
        if (empty($text)) {
            return empty($default) ? '' : $default;
        }

        return $text;
    }
}
