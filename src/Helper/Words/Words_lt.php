<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Lithuanian.
 *
 * @category Numbers
 *
 * @author   Laurynas Butkus <lauris@night.lt>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_lt extends Words
{
    // {{{ properties

    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'lt';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Lithuanian';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'lietuviđkai';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural).
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['tűkstantis', 'tűkstančiai', 'tűkstančiř'],
        6 => ['milijonas', 'milijonai', 'milijonř'],
        9 => ['bilijonas', 'bilijonai', 'bilijonř'],
        12 => ['trilijonas', 'trilijonai', 'trilijonř'],
        15 => ['kvadrilijonas', 'kvadrilijonai', 'kvadrilijonř'],
        18 => ['kvintilijonas', 'kvintilijonai', 'kvintilijonř'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'nulis', 'vienas', 'du', 'trys', 'keturi',
        'penki', 'đeđi', 'septyni', 'ađtuoni', 'devyni',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = ' ';

    /**
     * The default currency name.
     *
     * @var string
     */
    public $def_currency = 'LTL';

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in Lithuanian language.
     *
     * @param int $num       An integer between -infinity and infinity inclusive :)
     *                       that need to be converted to words
     * @param int $power     the power of ten for the rest of the number to the right.
     *                       Optional, defaults to 0
     * @param int $powsuffix The power name to be added to the end of the return string.
     *                       Used internally. Optional, defaults to ''.
     *
     * @return string The corresponding word representation
     *
     * @author Laurynas Butkus <lauris@night.lt>
     *
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num, $power = 0, $powsuffix = '')
    {
        $ret = '';

        // add a minus sign
        if ('-' === substr($num, 0, 1)) {
            $ret = $this->_sep . $this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs and spaces
        $num = trim($num);
        $num = preg_replace('/^0+/', '', $num);

        if (\strlen($num) > 3) {
            $maxp = \strlen($num) - 1;
            $curp = $maxp;
            for ($p = $maxp; $p > 0; --$p) { // power
                // check for highest power
                if (isset($this->_exponent[$p])) {
                    // send substr from $curp to $p
                    $snum = substr($num, $maxp - $curp, $curp - $p + 1);
                    $snum = preg_replace('/^0+/', '', $snum);
                    if ('' !== $snum) {
                        $cursuffix = $this->_exponent[$power][\count($this->_exponent[$power]) - 1];
                        if ('' !== $powsuffix) {
                            $cursuffix .= $this->_sep . $powsuffix;
                        }

                        $ret .= $this->_toWords($snum, $p, $cursuffix);
                    }
                    $curp = $p - 1;

                    continue;
                }
            }
            $num = substr($num, $maxp - $curp, $curp - $p + 1);
            if (0 === $num) {
                return $ret;
            }
        } elseif (0 === $num || '' === $num) {
            return $this->_sep . $this->_digits[0];
        }

        $h = $t = $d = 0;

        switch (\strlen($num)) {
            case 3:
                $h = (int) substr($num, -3, 1);

                // no break
            case 2:
                $t = (int) substr($num, -2, 1);

                // no break
            case 1:
                $d = (int) substr($num, -1, 1);

                break;
            case 0:
                return;

                break;
        }

        if ($h > 1) {
            $ret .= $this->_sep . $this->_digits[$h] . $this->_sep . 'đimtai';
        } elseif ($h) {
            $ret .= $this->_sep . 'đimtas';
        }

        // ten, twenty etc.
        switch ($t) {
            case 9:
                $ret .= $this->_sep . 'devyniasdeđimt';

                break;
            case 8:
                $ret .= $this->_sep . 'ađtuoniasdeđimt';

                break;
            case 7:
                $ret .= $this->_sep . 'septyniasdeđimt';

                break;
            case 6:
                $ret .= $this->_sep . 'đeđiasdeđimt';

                break;
            case 5:
                $ret .= $this->_sep . 'penkiasdeđimt';

                break;
            case 4:
                $ret .= $this->_sep . 'keturiasdeđimt';

                break;
            case 3:
                $ret .= $this->_sep . 'trisdeđimt';

                break;
            case 2:
                $ret .= $this->_sep . 'dvideđimt';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep . 'deđimt';

                        break;
                    case 1:
                        $ret .= $this->_sep . 'vienuolika';

                        break;
                    case 2:
                        $ret .= $this->_sep . 'dvylika';

                        break;
                    case 3:
                        $ret .= $this->_sep . 'trylika';

                        break;
                    case 4:
                        $ret .= $this->_sep . 'keturiolika';

                        break;
                    case 5:
                        $ret .= $this->_sep . 'penkiolika';

                        break;
                    case 6:
                        $ret .= $this->_sep . 'đeđiolika';

                        break;
                    case 7:
                        $ret .= $this->_sep . 'septyniolika';

                        break;
                    case 8:
                        $ret .= $this->_sep . 'ađtuoniolika';

                        break;
                    case 9:
                        $ret .= $this->_sep . 'devyniolika';

                        break;
                }

                break;
        }

        // add digits only in <0>,<1,9> and <21,inf>
        if (1 !== $t && $d > 0) {
            if ($d > 1 || !$power || $t) {
                $ret .= $this->_sep . $this->_digits[$d];
            }
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !\is_array($lev)) {
                return null;
            }

            //echo " $t $d  <br>";

            if (1 === $t || ($t > 0 && 0 === $d)) {
                $ret .= $this->_sep . $lev[2];
            } elseif ($d > 1) {
                $ret .= $this->_sep . $lev[1];
            } else {
                $ret .= $this->_sep . $lev[0];
            }
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep . $powsuffix;
        }

        return $ret;
    }
}
