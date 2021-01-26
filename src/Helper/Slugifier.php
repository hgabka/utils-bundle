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
        setlocale(\LC_ALL, 'hu_HU.utf-8');
        $subst = substr($delimiter, 0, 1);
        // replace all non letters or digits by -
        $text = preg_replace('~[^\\pL0-9]+~u', $subst, $text); // substitutes anything but letters, numbers and '_' with separator
        $text = trim($text, $delimiter);
        $origText = $text;
        $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text); // TRANSLIT does the whole job
        if (empty($text)) {
            $text = Transliterator::transliterate($origText, $delimiter);
        }

        $text = strtolower($text);
        $text = preg_replace('~[^-a-z0-9_'.$subst.']+~', '', $text); // keep only letters, numbers, '_' and separator  $text = preg_replace('~[^\\pL0-9_]+~u', '-', $text); // substitutes anything but letters, numbers and '_' with separator
        $text = trim($text, $delimiter);
        $origText = $text;
        $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text); // TRANSLIT does the whole job
        if (empty($text)) {
            $text = Transliterator::transliterate($origText, $delimiter);
        }
        $text = strtolower($text);
        $text = preg_replace('~[^-a-z0-9_'.$subst.']+~', '', $text); // keep only letters, numbers, '_' and separator

        if (empty($text)) {
            return empty($default) ? '' : $default;
        }

        return $text;
    }
}
