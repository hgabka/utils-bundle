<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Donald Knuth system, in English language.
 *
 * @category Numbers
 *
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_en_100 extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'en_100';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'English (Donald Knuth system)';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'English (Donald Knuth system)';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural)
     * Names based on:
     * http://home.earthlink.net/~mrob/pub/math/largenum.html
     * Donald Knuth system (power of 2).
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        2 => ['hundred'],
        4 => ['myriad'],
        8 => ['myllion'],
       16 => ['byllion'],
       32 => ['tryllion'],
       64 => ['quadryllion'],
      128 => ['quintyllion'],
      256 => ['sextyllion'],
      512 => ['septyllion'],
     1024 => ['octyllion'],
     2048 => ['nonyllion'],
     4096 => ['decyllion'],
     8192 => ['undecyllion'],
    16384 => ['duodecyllion'],
    32768 => ['tredecyllion'],
    65536 => ['quattuordecyllion'],
   131072 => ['quindecyllion'],
   262144 => ['sexdecyllion'],
   524288 => ['septendecyllion'],
  1048576 => ['octodecyllion'],
  2097152 => ['novemdecyllion'],
  4194304 => ['vigintyllion'],
        ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'zero', 'one', 'two', 'three', 'four',
        'five', 'six', 'seven', 'eight', 'nine',
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
     * in Donald Knuth system, in English language.
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
            $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'hundred';

            // in English only - add ' and' for [1-9]01..[1-9]99
            // (also for 1001..1099, 10001..10099 but it is harder)
            // for now it is switched off, maybe some language purists
            // can force me to enable it, or to remove it completely
            // if (($t + $d) > 0)
            //   $ret .= $this->_sep . 'and';
        }

        // ten, twenty etc.
        switch ($t) {
        case 9:
        case 7:
        case 6:
            $ret .= $this->_sep.$this->_digits[$t].'ty';

            break;
        case 8:
            $ret .= $this->_sep.'eighty';

            break;
        case 5:
            $ret .= $this->_sep.'fifty';

            break;
        case 4:
            $ret .= $this->_sep.'forty';

            break;
        case 3:
            $ret .= $this->_sep.'thirty';

            break;
        case 2:
            $ret .= $this->_sep.'twenty';

            break;
        case 1:
            switch ($d) {
            case 0:
                $ret .= $this->_sep.'ten';

                break;
            case 1:
                $ret .= $this->_sep.'eleven';

                break;
            case 2:
                $ret .= $this->_sep.'twelve';

                break;
            case 3:
                $ret .= $this->_sep.'thirteen';

                break;
            case 4:
            case 6:
            case 7:
            case 9:
                $ret .= $this->_sep.$this->_digits[$d].'teen';

                break;
            case 5:
                $ret .= $this->_sep.'fifteen';

                break;
            case 8:
                $ret .= $this->_sep.'eighteen';

                break;
            }

            break;
        }

        if (1 !== $t && $d > 0) { // add digits only in <0>,<1,9> and <21,inf>
            // add minus sign between [2-9] and digit
            if ($t > 1) {
                $ret .= '-'.$this->_digits[$d];
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
