<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Italian.
 * It supports up to quadrilions.
 *
 * @category Numbers
 *
 * @author   Filippo Beltramini <phil@esight.it>
 * @author   Davide Caironi     <cairo@esight.it>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_it extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'it';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Italian';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Italiano';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'meno ';

    /**
     * The sufixes for exponents (singular and plural).
     *
     * @var array
     */
    public $_exponent = [
        0 => ['', ''],
        3 => ['mille', 'mila'],
        6 => ['milione', 'miloni'],
        12 => ['miliardo', 'miliardi'],
        18 => ['trillone', 'trilloni'],
        24 => ['quadrilione', 'quadrilioni'],
    ];
    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'zero', 'uno', 'due', 'tre', 'quattro',
        'cinque', 'sei', 'sette', 'otto', 'nove',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = '';

    /**
     * Converts a number to its word representation
     * in italiano.
     *
     * @param int $num   An integer between -infinity and infinity inclusive :)
     *                   that should be converted to a words representation
     * @param int $power The power of ten for the rest of the number to the right.
     *                   For example toWords(12,3) should give "doce mil".
     *                   Optional, defaults to 0.
     *
     * @return string The corresponding word representation
     *
     * @author Filippo Beltramini
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
            return ' '.$this->_digits[0].' ';
            $current_power = strlen($num);
        } else {
            $current_power = strlen($num);
        }

        // See if we need "thousands"
        $thousands = floor($num / 1000);
        if (1 === $thousands) {
            $ret .= $this->_sep.'mille'.$this->_sep;
        } elseif ($thousands > 1) {
            $ret .= $this->_toWords($thousands, 3).$this->_sep; //. 'mil' . $this->_sep;
        }

        // values for digits, tens and hundreds
        $h = floor(($num / 100) % 10);
        $t = floor(($num / 10) % 10);
        $d = floor($num % 10);

        // centinaia: duecento, trecento, etc...
        switch ($h) {
            case 1:
                if ((0 === $d) and (0 === $t)) { // is it's '100' use 'cien'
                    $ret .= $this->_sep.'cento';
                } else {
                    $ret .= $this->_sep.'cento';
                }

                break;
            case 2:
            case 3:
            case 4:
            case 6:
            case 8:
                $ret .= $this->_sep.$this->_digits[$h].'cento';

                break;
            case 5:
                $ret .= $this->_sep.'cinquecento';

                break;
            case 7:
                $ret .= $this->_sep.'settecento';

                break;
            case 9:
                $ret .= $this->_sep.'novecento';

                break;
        }

        // decine: venti trenta, etc...
        switch ($t) {
            case 9:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'novant';

                        break;
                    default:
                        $ret .= $this->_sep.'novanta';

                        break;
                }

                break;
            case 8:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'ottant';

                        break;
                    default:
                        $ret .= $this->_sep.'ottanta';

                        break;
                }

                break;
            case 7:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'settant';

                        break;
                    default:
                        $ret .= $this->_sep.'settanta';

                        break;
                }

                break;
            case 6:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'sessant';

                        break;
                    default:
                        $ret .= $this->_sep.'sessanta';

                        break;
                }

                break;
            case 5:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'cinquant';

                        break;
                    default:
                        $ret .= $this->_sep.'cinquanta';

                        break;
                }

                break;
            case 4:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'quarant';

                        break;
                    default:
                        $ret .= $this->_sep.'quaranta';

                        break;
                }

                break;
            case 3:
                switch ($d) {
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'trent';

                        break;
                    default:
                        $ret .= $this->_sep.'trenta';

                        break;
                }

                break;
            case 2:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'venti';

                        break;
                    case 1:
                    case 8:
                        $ret .= $this->_sep.'vent'.$this->_digits[$d];

                        break;
                    default:
                        $ret .= $this->_sep.'venti'.$this->_digits[$d];

                        break;
                }

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'dieci';

                        break;
                    case 1:
                        $ret .= $this->_sep.'undici';

                        break;
                    case 2:
                        $ret .= $this->_sep.'dodici';

                        break;
                    case 3:
                        $ret .= $this->_sep.'tredici';

                        break;
                    case 4:
                        $ret .= $this->_sep.'quattordici';

                        break;
                    case 5:
                        $ret .= $this->_sep.'quindici';

                        break;
                    case 6:
                        $ret .= $this->_sep.'sedici';

                        break;
                    case 7:
                        $ret .= $this->_sep.'diciassette';

                        break;
                    case 8:
                        $ret .= $this->_sep.'diciotto';

                        break;
                    case 9:
                        $ret .= $this->_sep.'diciannove';

                        break;
                }

                break;
        }

        // add digits only if it is a multiple of 10 and not 1x or 2x
        if ((1 !== $t) and (2 !== $t) and ($d > 0)) {
            // don't add 'e' for numbers below 10
            if (0 !== $t) {
                // use 'un' instead of 'uno' when there is a suffix ('mila', 'milloni', etc...)
                if (($power > 0) and (1 === $d)) {
                    $ret .= $this->_sep.' e un';
                } else {
                    $ret .= $this->_sep.''.$this->_digits[$d];
                }
            } else {
                if (($power > 0) and (1 === $d)) {
                    $ret .= $this->_sep.'un ';
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
