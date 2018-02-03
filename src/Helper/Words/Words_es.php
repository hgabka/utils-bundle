<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Spanish (Castellano).
 * It supports up to decallones (10^6).
 * It doesn't support spanish tonic accents (acentos).
 *
 * @category Numbers
 *
 * @author   Xavier Noguer
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_es extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'es';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Spanish';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Espańol';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'menos';

    /**
     * The sufixes for exponents (singular and plural).
     *
     * @var array
     */
    public $_exponent = [
        0 => ['', ''],
        3 => ['mil', 'mil'],
        6 => ['millón', 'millones'],
        12 => ['billón', 'billones'],
        18 => ['trilón', 'trillones'],
        24 => ['cuatrillón', 'cuatrillones'],
        30 => ['quintillón', 'quintillones'],
        36 => ['sextillón', 'sextillones'],
        42 => ['septillón', 'septillones'],
        48 => ['octallón', 'octallones'],
        54 => ['nonallón', 'nonallones'],
        60 => ['decallón', 'decallones'],
    ];
    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'cero', 'uno', 'dos', 'tres', 'cuatro',
        'cinco', 'seis', 'siete', 'ocho', 'nueve',
    ];
    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = ' ';

    /**
     * Converts a number to its word representation
     * in Spanish (Castellano).
     *
     * @param int $num   An integer between -infinity and infinity inclusive :)
     *                   that should be converted to a words representation
     * @param int $power The power of ten for the rest of the number to the right.
     *                   For example toWords(12,3) should give "doce mil".
     *                   Optional, defaults to 0.
     *
     * @return string The corresponding word representation
     *
     * @author Xavier Noguer
     *
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num, $power = 0)
    {
        // The return string;
        $ret = '';

        // add a the word for the minus sign if necessary
        if ('-' === substr($num, 0, 1)) {
            $ret = $this->_sep.$this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs
        $num = preg_replace('/^0+/', '', $num);

        if (strlen($num) > 6) {
            $current_power = 6;
            // check for highest power
            if (isset($this->_exponent[$power])) {
                // convert the number above the first 6 digits
                // with it's corresponding $power.
                $snum = substr($num, 0, -6);
                $snum = preg_replace('/^0+/', '', $snum);
                if ('' !== $snum) {
                    $ret .= $this->_toWords($snum, $power + 6);
                }
            }
            $num = substr($num, -6);
            if (0 === $num) {
                return $ret;
            }
        } elseif (0 === $num || '' === $num) {
            return ' '.$this->_digits[0];
            $current_power = strlen($num);
        } else {
            $current_power = strlen($num);
        }

        // See if we need "thousands"
        $thousands = floor($num / 1000);
        if (1 === $thousands) {
            $ret .= $this->_sep.'mil';
        } elseif ($thousands > 1) {
            $ret .= $this->_toWords($thousands, 3);
        }

        // values for digits, tens and hundreds
        $h = floor(($num / 100) % 10);
        $t = floor(($num / 10) % 10);
        $d = floor($num % 10);

        // cientos: doscientos, trescientos, etc...
        switch ($h) {
            case 1:
                if ((0 === $d) and (0 === $t)) { // is it's '100' use 'cien'
                    $ret .= $this->_sep.'cien';
                } else {
                    $ret .= $this->_sep.'ciento';
                }

                break;
            case 2:
            case 3:
            case 4:
            case 6:
            case 8:
                $ret .= $this->_sep.$this->_digits[$h].'cientos';

                break;
            case 5:
                $ret .= $this->_sep.'quinientos';

                break;
            case 7:
                $ret .= $this->_sep.'setecientos';

                break;
            case 9:
                $ret .= $this->_sep.'novecientos';

                break;
        }

        // decenas: veinte, treinta, etc...
        switch ($t) {
            case 9:
                $ret .= $this->_sep.'noventa';

                break;
            case 8:
                $ret .= $this->_sep.'ochenta';

                break;
            case 7:
                $ret .= $this->_sep.'setenta';

                break;
            case 6:
                $ret .= $this->_sep.'sesenta';

                break;
            case 5:
                $ret .= $this->_sep.'cincuenta';

                break;
            case 4:
                $ret .= $this->_sep.'cuarenta';

                break;
            case 3:
                $ret .= $this->_sep.'treinta';

                break;
            case 2:
                if (0 === $d) {
                    $ret .= $this->_sep.'veinte';
                } else {
                    if (($power > 0) and (1 === $d)) {
                        $ret .= $this->_sep.'veintiún';
                    } else {
                        $ret .= $this->_sep.'veinti'.$this->_digits[$d];
                    }
                }

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'diez';

                        break;
                    case 1:
                        $ret .= $this->_sep.'once';

                        break;
                    case 2:
                        $ret .= $this->_sep.'doce';

                        break;
                    case 3:
                        $ret .= $this->_sep.'trece';

                        break;
                    case 4:
                        $ret .= $this->_sep.'catorce';

                        break;
                    case 5:
                        $ret .= $this->_sep.'quince';

                        break;
                    case 6:
                    case 7:
                    case 9:
                    case 8:
                        $ret .= $this->_sep.'dieci'.$this->_digits[$d];

                        break;
                }

                break;
        }

        // add digits only if it is a multiple of 10 and not 1x or 2x
        if ((1 !== $t) and (2 !== $t) and ($d > 0)) {
            // don't add 'y' for numbers below 10
            if (0 !== $t) {
                // use 'un' instead of 'uno' when there is a suffix ('mil', 'millones', etc...)
                if (($power > 0) and (1 === $d)) {
                    $ret .= $this->_sep.' y un';
                } else {
                    $ret .= $this->_sep.'y '.$this->_digits[$d];
                }
            } else {
                if (($power > 0) and (1 === $d)) {
                    $ret .= $this->_sep.'un';
                } else {
                    $ret .= $this->_sep.$this->_digits[$d];
                }
            }
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !is_array($lev)) {
                return null;
            }

            // if it's only one use the singular suffix
            if ((1 === $d) and (0 === $t) and (0 === $h)) {
                $suffix = $lev[0];
            } else {
                $suffix = $lev[1];
            }
            if (0 !== $num) {
                $ret .= $this->_sep.$suffix;
            }
        }

        return $ret;
    }
}
