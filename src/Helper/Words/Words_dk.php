<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Danish.
 *
 * @category Numbers
 *
 * @author   Jesper Veggerby <pear.nosey@veggerby.dk>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_dk extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'dk';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Danish';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Dansk';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural).
     * From: http://da.wikipedia.org/wiki/Navne_p%E5_store_tal.
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['tusind', 'tusinde'],
        6 => ['million', 'millioner'],
        9 => ['milliard', 'milliarder'],
        12 => ['billion', 'billioner'],
        15 => ['billiard', 'billiarder'],
        18 => ['trillion', 'trillioner'],
        21 => ['trilliard', 'trilliarder'],
        24 => ['quadrillion', 'quadrillioner'],
        30 => ['quintillion', 'quintillioner'],
        36 => ['sextillion', 'sextillioner'],
        42 => ['septillion', 'septillioner'],
        48 => ['octillion', 'octillioner'],
        54 => ['nonillion', 'nonillioner'],
        60 => ['decillion', 'decillioner'],
        120 => ['vigintillion', 'vigintillioner'],
        600 => ['centillion', 'centillioner'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'nul', 'en', 'to', 'tre', 'fire',
        'fem', 'seks', 'syv', 'otte', 'ni',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = ' ';

    /**
     * The currency names (based on the below links,
     * informations from central bank websites and on encyclopedias).
     *
     * @var array
     *
     * @see http://da.wikipedia.org/wiki/Valuta
     */
    public $_currency_names = [
        'AUD' => [['australsk dollar', 'australske dollars'], ['cent']],
        'CAD' => [['canadisk dollar', 'canadisk dollars'], ['cent']],
        'CHF' => [['schweitzer franc'], ['rappen']],
        'CYP' => [['cypriotisk pund', 'cypriotiske pund'], ['cent']],
        'CZK' => [['tjekkisk koruna'], ['halerz']],
        'DKK' => [['krone', 'kroner'], ['řre']],
        'EUR' => [['euro'], ['euro-cent']],
        'GBP' => [['pund'], ['pence']],
        'HKD' => [['Hong Kong dollar', 'Hong Kong dollars'], ['cent']],
        'JPY' => [['yen'], ['sen']],
        'NOK' => [['norsk krone', 'norske kroner'], ['řre']],
        'PLN' => [['zloty', 'zlotys'], ['grosz']],
        'SEK' => [['svensk krone', 'svenske kroner'], ['řre']],
        'USD' => [['dollar', 'dollars'], ['cent']],
    ];

    /**
     * The default currency name.
     *
     * @var string
     */
    public $def_currency = 'DKK'; // Danish krone

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in Danish language.
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
     * @author Jesper Veggerby <pear.nosey@veggerby.dk>
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
            if (1 === $h) {
                $ret .= $this->_sep.'et'.$this->_sep.'hundrede';
            } else {
                $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'hundrede';
            }

            //if (($t + $d) > 0)
            //  $ret .= $this->_sep . 'og';
        } elseif ((isset($maxp)) && ($maxp > 3)) {
            // add 'og' in the case where there are preceding thousands but not hundreds or tens,
            // so fx. 80001 becomes 'firs tusinde og en' instead of 'firs tusinde en'
            $ret .= $this->_sep.'og';
        }

        if (1 !== $t && $d > 0) {
            $ret .= $this->_sep.((1 === $d & 3 === $power && 0 === $t && 0 === $h) ? 'et' : $this->_digits[$d]).($t > 1 ? $this->_sep.'og' : '');
        }

        // ten, twenty etc.
        switch ($t) {
            case 9:
                $ret .= $this->_sep.'halvfems';

                break;
            case 8:
                $ret .= $this->_sep.'firs';

                break;
            case 7:
                $ret .= $this->_sep.'halvfjerds';

                break;
            case 6:
                $ret .= $this->_sep.'tres';

                break;
            case 5:
                $ret .= $this->_sep.'halvtreds';

                break;
            case 4:
                $ret .= $this->_sep.'fyrre';

                break;
            case 3:
                $ret .= $this->_sep.'tredive';

                break;
            case 2:
                $ret .= $this->_sep.'tyve';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'ti';

                        break;
                    case 1:
                        $ret .= $this->_sep.'elleve';

                        break;
                    case 2:
                        $ret .= $this->_sep.'tolv';

                        break;
                    case 3:
                        $ret .= $this->_sep.'tretten';

                        break;
                    case 4:
                        $ret .= $this->_sep.'fjorten';

                        break;
                    case 5:
                        $ret .= $this->_sep.'femten';

                        break;
                    case 6:
                        $ret .= $this->_sep.'seksten';

                        break;
                    case 7:
                        $ret .= $this->_sep.'sytten';

                        break;
                    case 8:
                        $ret .= $this->_sep.'atten';

                        break;
                    case 9:
                        $ret .= $this->_sep.'nitten';

                        break;
                }

                break;
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !\is_array($lev)) {
                return null;
            }

            if (1 === $d && 0 === ($t + $h)) {
                $ret .= $this->_sep.$lev[0];
            } else {
                $ret .= $this->_sep.$lev[1];
            }
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep.$powsuffix;
        }

        return $ret;
    }

    // }}}
    // {{{ toCurrencyWords()

    /**
     * Converts a currency value to its word representation
     * (with monetary units) in danish language.
     *
     * @param int $int_curr         An international currency symbol
     *                              as defined by the ISO 4217 standard (three characters)
     * @param int $decimal          A money total amount without fraction part (e.g. amount of dollars)
     * @param int $fraction         Fractional part of the money amount (e.g. amount of cents)
     *                              Optional. Defaults to false.
     * @param int $convert_fraction Convert fraction to words (left as numeric if set to false).
     *                              Optional. Defaults to true.
     *
     * @return string The corresponding word representation for the currency
     *
     * @author Jesper Veggerby <pear.nosey@veggerby.dk>
     *
     * @since  Numbers_Words 0.4
     */
    public function toCurrencyWords($int_curr, $decimal, $fraction = false, $convert_fraction = true)
    {
        $int_curr = strtoupper($int_curr);
        if (!isset($this->_currency_names[$int_curr])) {
            $int_curr = $this->def_currency;
        }
        $curr_names = $this->_currency_names[$int_curr];

        if (('' !== $decimal) and (0 !== $decimal)) {
            $ret = trim($this->_toWords($decimal));
            $lev = (1 === $decimal) ? 0 : 1;
            if ($lev > 0) {
                if (\count($curr_names[0]) > 1) {
                    $ret .= $this->_sep.$curr_names[0][$lev];
                } else {
                    $ret .= $this->_sep.$curr_names[0][0];
                }
            } else {
                $ret .= $this->_sep.$curr_names[0][0];
            }

            if ((false !== $fraction) and (0 !== $fraction)) {
                $ret .= $this->_sep.'og';
            }
        }

        if ((false !== $fraction) and (0 !== $fraction)) {
            if ($convert_fraction) {
                $ret .= $this->_sep.trim($this->_toWords($fraction));
            } else {
                $ret .= $this->_sep.$fraction;
            }
            $lev = (1 === $fraction) ? 0 : 1;
            if ($lev > 0) {
                if (\count($curr_names[1]) > 1) {
                    $ret .= $this->_sep.$curr_names[1][$lev];
                } else {
                    $ret .= $this->_sep.$curr_names[1][0];
                }
            } else {
                $ret .= $this->_sep.$curr_names[1][0];
            }
        }

        return $ret;
    }
}
