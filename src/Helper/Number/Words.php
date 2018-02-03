<?php

namespace Hgabka\UtilsBundle\Helper\Number;

use Hgabka\UtilsBundle\Helper\Math\BigInteger;

class Words
{
    /**
     * Default Locale name.
     *
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * Converts a number to its word representation.
     *
     * @param int    $num     An integer between -infinity and infinity inclusive :)
     *                        that should be converted to a words representation
     * @param string $locale  Language name abbreviation. Optional. Defaults to
     *                        current loaded driver or en_US if any.
     * @param array  $options Specific driver options
     *
     * @return string The corresponding word representation
     */
    public function toWords($num, $locale = '', $options = [])
    {
        if (empty($locale)) {
            $locale = $this->locale;
        }

        if (empty($locale)) {
            $locale = 'en_US';
        }

        $classname = "Hgabka\KunstmaanExtensionBundle\Helper\Words\Words_${locale}";

        if (!class_exists($classname)) {
            return $this->raiseError("Unable to include the Numbers/Words/lang.${locale}.php file");
        }

        $methods = get_class_methods($classname);

        if (!in_array('_toWords', $methods, true) && !in_array('_towords', $methods, true)) {
            return $this->raiseError("Unable to find _toWords method in '$classname' class");
        }

        if (!is_int($num)) {
            // cast (sanitize) to int without losing precision
            $num = preg_replace('/^[^\d]*?(-?)[ \t\n]*?(\d+)([^\d].*?)?$/', '$1$2', $num);
        }

        $truth_table = ($classname === get_class($this)) ? 'T' : 'F';
        $truth_table .= (empty($options)) ? 'T' : 'F';

        switch ($truth_table) {
        // We are a language driver
        case 'TT':
            return trim($this->_toWords($num));
            break;
        // We are a language driver with custom options
        case 'TF':
            return trim($this->_toWords($num, $options));
            break;
        // We are the parent class
        case 'FT':
            @$obj = new $classname();

            return trim($obj->_toWords($num));
            break;
        // We are the parent class and should pass driver options
        case 'FF':
            @$obj = new $classname();

            return trim($obj->_toWords($num, $options));
            break;
        }
    }

    /**
     * Converts a currency value to word representation (1.02 => one dollar two cents)
     * If the number has not any fraction part, the "cents" number is omitted.
     *
     * @param float  $num      A float/integer/string number representing currency value
     * @param string $locale   Language name abbreviation. Optional. Defaults to en_US.
     * @param string $int_curr International currency symbol
     *                         as defined by the ISO 4217 standard (three characters).
     *                         E.g. 'EUR', 'USD', 'PLN'. Optional.
     *                         Defaults to $def_currency defined in the language class.
     *
     * @return string The corresponding word representation
     */
    public function toCurrency($num, $locale = 'en_US', $int_curr = '')
    {
        $ret = $num;

        $classname = "Hgabka\KunstmaanExtensionBundle\Helper\Words\Words_${locale}";

        if (!class_exists($classname)) {
            return $this->raiseError("Unable to include the Numbers/Words/lang.${locale}.php file");
        }

        $methods = get_class_methods($classname);

        if (!in_array('toCurrencyWords', $methods, true) && !in_array('tocurrencywords', $methods, true)) {
            return $this->raiseError("Unable to find toCurrencyWords method in '$classname' class");
        }

        @$obj = new $classname();

        // round if a float is passed, use Math_BigInteger otherwise
        if (is_float($num)) {
            $num = round($num, 2);
        }

        if (false === strpos($num, '.')) {
            return trim($obj->toCurrencyWords($int_curr, $num));
        }

        $currency = explode('.', $num, 2);

        $len = strlen($currency[1]);

        if (1 === $len) {
            // add leading zero
            $currency[1] .= '0';
        } elseif ($len > 2) {
            // get the 3rd digit after the comma
            $round_digit = substr($currency[1], 2, 1);

            // cut everything after the 2nd digit
            $currency[1] = substr($currency[1], 0, 2);

            if ($round_digit >= 5) {
                // round up without losing precision

                $int = new BigInteger(implode($currency));
                $int = $int->add(new BigInteger(1));
                $int_str = $int->toString();

                $currency[0] = substr($int_str, 0, -2);
                $currency[1] = substr($int_str, -2);

                // check if the rounded decimal part became zero
                if ('00' === $currency[1]) {
                    $currency[1] = false;
                }
            }
        }

        return trim($obj->toCurrencyWords($int_curr, $currency[0], $currency[1]));
    }

    /**
     * Trigger a PEAR error.
     *
     * To improve performances, the PEAR.php file is included dynamically.
     *
     * @param string $msg error message
     *
     * @return PEAR_Error
     */
    public function raiseError($msg)
    {
        throw new \Exception($msg);
    }

    /**
     * Lists available locales for Numbers_Words.
     *
     * @param mixed $locale string/array of strings $locale
     *                      Optional searched language name abbreviation.
     *                      Default: all available locales.
     *
     * @return array   The available locales (optionaly only the requested ones)
     * @return mixed[]
     */
    protected function getLocales($locale = null)
    {
        $ret = [];
        if (isset($locale) && is_string($locale)) {
            $locale = [$locale];
        }

        $dname = __DIR__.DIRECTORY_SEPARATOR.'Words'.DIRECTORY_SEPARATOR;

        $dh = opendir($dname);

        if ($dh) {
            while ($fname = readdir($dh)) {
                if (preg_match('#^Words_\.([a-z_]+)\.php$#i', $fname, $matches)) {
                    if (is_file($dname.$fname) && is_readable($dname.$fname) &&
                        (!isset($locale) || in_array($matches[1], $locale, true))) {
                        $ret[] = $matches[1];
                    }
                }
            }
            closedir($dh);
            sort($ret);
        }

        return $ret;
    }

    // }}}
}

// }}}
