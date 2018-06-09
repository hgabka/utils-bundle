<?php

namespace Hgabka\UtilsBundle\Helper\Words;

use Hgabka\UtilsBundle\Helper\Number\Words;

/**
 * Class for translating numbers into German.
 *
 * @category Numbers
 *
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_de extends Words
{
    // {{{ properties

    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'de';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'German';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Deutsch';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'Minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural)
     * Names partly based on:
     * http://german.about.com/library/blzahlenaud.htm
     * http://www3.osk.3web.ne.jp/~nagatani/common/zahlwort.htm.
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['tausend', 'tausend'],
        6 => ['Million', 'Millionen'],
        9 => ['Milliarde', 'Milliarden'],
       12 => ['Billion', 'Billionen'],
       15 => ['Billiarde', 'Billiarden'],
       18 => ['Trillion', 'Trillionen'],
       21 => ['Trilliarde', 'Trilliarden'],
       24 => ['Quadrillion', 'Quadrillionen'],
       27 => ['Quadrilliarde', 'Quadrilliarden'],
       30 => ['Quintillion', 'Quintillionen'],
       33 => ['Quintilliarde', 'Quintilliarden'],
       36 => ['Sextillion', 'Sextillionen'],
       39 => ['Sextilliarde', 'Sextilliarden'],
       42 => ['Septillion', 'Septillionen'],
       45 => ['Septilliarde', 'Septilliarden'],
       48 => ['Oktillion', 'Oktillionen'], // oder Octillionen
       51 => ['Oktilliarde', 'Oktilliarden'],
       54 => ['Nonillion', 'Nonillionen'],
       57 => ['Nonilliarde', 'Nonilliarden'],
       60 => ['Dezillion', 'Dezillionen'],
       63 => ['Dezilliarde', 'Dezilliarden'],
      120 => ['Vigintillion', 'Vigintillionen'],
      123 => ['Vigintilliarde', 'Vigintilliarden'],
      600 => ['Zentillion', 'Zentillionen'], // oder Centillion
      603 => ['Zentilliarde', 'Zentilliarden'],
        ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'null', 'ein', 'zwei', 'drei', 'vier',
        'fünf', 'sechs', 'sieben', 'acht', 'neun',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = '';

    /**
     * The exponent word separator.
     *
     * @var string
     */
    public $_sep2 = ' ';

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in German language.
     *
     * @param int $num       An integer between -infinity and infinity inclusive :)
     *                       that need to be converted to words
     * @param int $power     The power of ten for the rest of the number to the right.
     *                       Optional, defaults to 0.
     * @param int $powsuffix The power name to be added to the end of the return string.
     *                       Used internally. Optional, defaults to ''.
     *
     * @return string The corresponding word representation
     *
     * @author Piotr Klaban <makler@man.torun.pl>
     *
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num, $power = 0, $powsuffix = '')
    {
        $ret = '';

        // add a minus sign
        if ('-' === substr($num, 0, 1)) {
            $ret = $this->_sep.$this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs and spaces
        $num = trim($num);
        $num = preg_replace('/^0+/', '', $num);

        if (strlen($num) > 3) {
            $maxp = strlen($num) - 1;
            $curp = $maxp;
            for ($p = $maxp; $p > 0; --$p) { // power
                // check for highest power
                if (isset($this->_exponent[$p])) {
                    // send substr from $curp to $p
                    $snum = substr($num, $maxp - $curp, $curp - $p + 1);
                    $snum = preg_replace('/^0+/', '', $snum);
                    if ('' !== $snum) {
                        $cursuffix = $this->_exponent[$power][count($this->_exponent[$power]) - 1];
                        if ('' !== $powsuffix) {
                            $cursuffix .= $this->_sep.$powsuffix;
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
            return $this->_sep.$this->_digits[0];
        }

        $h = $t = $d = 0;

        switch (strlen($num)) {
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
            $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'hundert';
        }

        if (1 !== $t && $d > 0) { // add digits only in <0>,<1,9> and <21,inf>
            if ($t > 0) {
                $ret .= $this->_digits[$d].'und';
            } else {
                $ret .= $this->_digits[$d];
                if (1 === $d) {
                    if (0 === $power) {
                        $ret .= 's'; // fuer eins
                    } else {
                        if (3 !== $power) {  // tausend ausnehmen
                            $ret .= 'e'; // fuer eine
                        }
                    }
                }
            }
        }

        // ten, twenty etc.
        switch ($t) {
        case 9:
        case 8:
        case 5:
            $ret .= $this->_sep.$this->_digits[$t].'zig';

            break;
        case 7:
            $ret .= $this->_sep.'siebzig';

            break;
        case 6:
            $ret .= $this->_sep.'sechzig';

            break;
        case 4:
            $ret .= $this->_sep.'vierzig';

            break;
        case 3:
            $ret .= $this->_sep.'dreißig';

            break;
        case 2:
            $ret .= $this->_sep.'zwanzig';

            break;
        case 1:
            switch ($d) {
            case 0:
                $ret .= $this->_sep.'zehn';

                break;
            case 1:
                $ret .= $this->_sep.'elf';

                break;
            case 2:
                $ret .= $this->_sep.'zwölf';

                break;
            case 3:
            case 4:
            case 5:
            case 8:
            case 9:
                $ret .= $this->_sep.$this->_digits[$d].'zehn';

                break;
            case 6:
                $ret .= $this->_sep.'sechzehn';

                break;
            case 7:
                $ret .= $this->_sep.'siebzehn';

                break;
            }

            break;
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !is_array($lev)) {
                return null;
            }

            if (3 === $power) {
                $ret .= $this->_sep.$lev[0];
            } elseif (1 === $d && 0 === ($t + $h)) {
                $ret .= $this->_sep2.$lev[0].$this->_sep2;
            } else {
                $ret .= $this->_sep2.$lev[1].$this->_sep2;
            }
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep.$powsuffix;
        }

        return $ret;
    }

    // }}}
}
