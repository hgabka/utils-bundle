<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Indonesian.
 *
 * @category Numbers
 *
 * @author   Ernas M. Jamil <ernasm@samba.co.id>
 * @author   Arif Rifai Dwiyanto
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_id extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'id';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Indonesia Language';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Bahasa Indonesia';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural)
     * Names partly based on:
     * http://www.users.dircon.co.uk/~shaunf/shaun/numbers/millions.htm.
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['ribu'],
        6 => ['juta'],
        9 => ['milyar'],
        12 => ['trilyun'],
        24 => ['quadrillion'],
        30 => ['quintillion'],
        36 => ['sextillion'],
        42 => ['septillion'],
        48 => ['octillion'],
        54 => ['nonillion'],
        60 => ['decillion'],
        66 => ['undecillion'],
        72 => ['duodecillion'],
        78 => ['tredecillion'],
        84 => ['quattuordecillion'],
        90 => ['quindecillion'],
        96 => ['sexdecillion'],
        102 => ['septendecillion'],
        108 => ['octodecillion'],
        114 => ['novemdecillion'],
        120 => ['vigintillion'],
        192 => ['duotrigintillion'],
        600 => ['centillion'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'nol', 'satu', 'dua', 'tiga', 'empat',
        'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
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
     * in Indonesian language.
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
     * @author Ernas M. Jamil
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

        if (strlen($num) > 4) {
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

        $h = $t = $d = $th = 0;

        switch (strlen($num)) {
            case 4:
                $th = (int) substr($num, -4, 1);

                // no break
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

        if ($th) {
            if (1 === $th) {
                $ret .= $this->_sep.'seribu';
            } else {
                $ret .= $this->_sep.$this->_digits[$th].$this->_sep.'ribu';
            }
        }

        if ($h) {
            if (1 === $h) {
                $ret .= $this->_sep.'seratus';
            } else {
                $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'ratus';
            }

            // in English only - add ' and' for [1-9]01..[1-9]99
            // (also for 1001..1099, 10001..10099 but it is harder)
            // for now it is switched off, maybe some language purists
            // can force me to enable it, or to remove it completely
            // if (($t + $d) > 0)
        }

        // ten, twenty etc.
        switch ($t) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
            case 4:
            case 3:
            case 2:
                $ret .= $this->_sep.$this->_digits[$t].' puluh';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'sepuluh';

                        break;
                    case 1:
                        $ret .= $this->_sep.'sebelas';

                        break;
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $ret .= $this->_sep.$this->_digits[$d].' belas';

                        break;
                }

                break;
        }

        if (1 !== $t && $d > 0) { // add digits only in <0>,<1,9> and <21,inf>
            // add minus sign between [2-9] and digit
            if ($t > 1) {
                $ret .= ' '.$this->_digits[$d];
            } else {
                $ret .= $this->_sep.$this->_digits[$d];
            }
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !is_array($lev)) {
                return null;
            }

            $ret .= $this->_sep.$lev[0];
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep.$powsuffix;
        }

        return $ret;
    }
}
