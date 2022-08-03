<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Czech.
 *
 * @category Numbers
 *
 * @author   Petr 'PePa' Pavel <petr.pavel@pepa.info>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_cs extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'cs';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Czech';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Czech';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'mínus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural)
     * Names partly based on:
     * http://cs.wikipedia.org/wiki/P%C5%99edpony_soustavy_SI
     * the rest was translated from lang.en_GB.php
     * names verified by "Ustav pro jazyk cesky" only up to Septilion
     * (they could verify only the lingual matter - not the mathematical one).
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['tisíc', 'tisíce', 'tisíc'],
        6 => ['milion', 'miliony', 'milionů'],
        9 => ['miliarda', 'miliardy', 'miliard'],
        12 => ['bilion', 'biliony', 'bilionů'],
        15 => ['biliarda', 'biliardy', 'biliard'],
        18 => ['trilion', 'triliony', 'trilionů'],
        21 => ['triliarda', 'triliardy', 'triliard'],

        24 => ['kvadrilion', 'kvadriliony', 'kvadrilionů'],
        30 => ['kvintilion', 'kvintiliony', 'kvintilionů'],
        36 => ['sextilion', 'sextiliony', 'sextilionů'],
        42 => ['septilion', 'septiliony', 'septilionů'],

        48 => ['oktilion', 'oktiliony', 'oktilionů'],
        54 => ['nonilion', 'noniliony', 'nonilionů'],
        60 => ['decilion', 'deciliony', 'decilionů'],

        66 => ['undecilion', 'undeciliony', 'undecilionů'],
        72 => ['duodecilion', 'duodeciliony', 'duodecilionů'],
        78 => ['tredecilion', 'tredeciliony', 'tredecilionů'],
        84 => ['kvatrodecilion', 'kvatrodeciliony', 'kvatrodecilionů'],
        90 => ['kvindecilion', 'kvindeciliony', 'kvindecilionů'],
        96 => ['sexdecilion', 'sexdeciliony', 'sexdecilionů'],
        102 => ['septendecilion', 'septendeciliony', 'septendecilionů'],
        108 => ['oktodecilion', 'oktodeciliony', 'oktodecilionů'],
        114 => ['novemdecilion', 'novemdeciliony', 'novemdecilionů'],
        120 => ['vigintilion', 'vigintiliony', 'vigintilionů'],
        192 => ['duotrigintilion', 'duotrigintiliony', 'duotrigintilionů'],
        600 => ['centilion', 'centiliony', 'centilionů'],
    ];

    /**
     * The array containing the forms of Czech word for "hundred".
     *
     * @var array
     */
    public $_hundreds = [
        0 => 'sto', 'stě', 'sta', 'set',
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'nula', 'jedna', 'dva', 'tři', 'čtyři',
        'pět', 'ąest', 'sedm', 'osm', 'devět',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = ' ';

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in Czech language.
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
     * @author Petr 'PePa' Pavel <petr.pavel@pepa.info>
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

        if ($h) {
            // inflection of the word "hundred"
            if (1 === $h) {
                $ret .= $this->_sep . $this->_hundreds[0];
            } elseif (2 === $h) {
                $ret .= $this->_sep . 'dvě' . $this->_sep . $this->_hundreds[1];
            } elseif (($h > 1) && ($h < 5)) {
                $ret .= $this->_sep . $this->_digits[$h] . $this->_sep . $this->_hundreds[2];
            } else {        // if ($h >= 5)
                $ret .= $this->_sep . $this->_digits[$h] . $this->_sep . $this->_hundreds[3];
            }
            // in English only - add ' and' for [1-9]01..[1-9]99
            // (also for 1001..1099, 10001..10099 but it is harder)
            // for now it is switched off, maybe some language purists
            // can force me to enable it, or to remove it completely
            // if (($t + $d) > 0)
            //   $ret .= $this->_sep . 'and';
        }

        // ten, twenty etc.
        switch ($t) {
            case 2:
            case 3:
            case 4:
                $ret .= $this->_sep . $this->_digits[$t] . 'cet';

                break;
            case 5:
                $ret .= $this->_sep . 'padesát';

                break;
            case 6:
                $ret .= $this->_sep . 'ąedesát';

                break;
            case 7:
                $ret .= $this->_sep . 'sedmdesát';

                break;
            case 8:
                $ret .= $this->_sep . 'osmdesát';

                break;
            case 9:
                $ret .= $this->_sep . 'devadesát';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep . 'deset';

                        break;
                    case 1:
                        $ret .= $this->_sep . 'jedenáct';

                        break;
                    case 4:
                        $ret .= $this->_sep . 'čtrnáct';

                        break;
                    case 5:
                        $ret .= $this->_sep . 'patnáct';

                        break;
                    case 9:
                        $ret .= $this->_sep . 'devatenáct';

                        break;
                    case 2:
                    case 3:
                    case 6:
                    case 7:
                    case 8:
                        $ret .= $this->_sep . $this->_digits[$d] . 'náct';

                        break;
                }

                break;
        }

        if ((1 !== $t) && ($d > 0) && ((0 === $power) || ($num > 1))) {
            $ret .= $this->_sep . $this->_digits[$d];
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !\is_array($lev)) {
                return null;
            }

            // inflection of exponental words
            if (1 === $num) {
                $idx = 0;
            } elseif ((($num > 1) && ($num < 5)) || (((int) ("$t$d") > 1) && ((int) ("$t$d") < 5))) {
                $idx = 1;
            } else {
                $idx = 2;
            }

            $ret .= $this->_sep . $lev[$idx];
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep . $powsuffix;
        }

        return $ret;
    }
}
