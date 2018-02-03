<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Swedish.
 *
 * @category Numbers
 *
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @author   Robin Ericsson <robin.ericsson@profecta.se>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_sv extends Words
{
    // {{{ properties

    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'sv';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Swedish';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Svenska';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'Minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural).
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['tusen', 'tusen'],
        6 => ['miljon', 'miljoner'],
        9 => ['miljard', 'miljarder'],
        12 => ['biljon', 'biljoner'],
        15 => ['biljard', 'biljarder'],
        18 => ['triljon', 'triljoner'],
        21 => ['triljard', 'triljarder'],
        24 => ['kvadriljon', 'kvadriljoner'],
        27 => ['kvadriljard', 'kvadriljarder'],
        30 => ['kvintiljon', 'kvintiljoner'],
        33 => ['kvintiljard', 'kvintiljarder'],
        36 => ['sextiljon', 'sextiljoner'],
        39 => ['sextiljard', 'sextiljarder'],
        42 => ['septiljon', 'septiljoner'],
        45 => ['septiljard', 'septiljarder'],
        48 => ['oktiljon', 'oktiljoner'],
        51 => ['oktiljard', 'oktiljarder'],
        54 => ['noniljon', 'noniljoner'],
        57 => ['noniljard', 'noniljarder'],
        60 => ['dekiljon', 'dekiljoner'],
        63 => ['dekiljard', 'dekiljarder'],
        120 => ['vigintiljon', 'vigintiljoner'],
        123 => ['vigintiljard', 'vigintiljarder'],
        600 => ['centiljon', 'centiljoner'],
        603 => ['centiljard', 'centiljarder'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'noll', 'ett', 'tvĺ', 'tre', 'fyra',
        'fem', 'sex', 'sju', 'ĺtta', 'nio',
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
     * in Swedish language.
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
     * @author Robin Ericsson <lobbin@localhost.nu>
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
            $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'hundra';
        }

        // ten, twenty etc.
        switch ($t) {
            case 5:
            case 6:
            case 7:
                $ret .= $this->_sep.$this->_digits[$t].'tio';

                break;
            case 9:
                $ret .= $this->_sep.'nittio';

                break;
            case 8:
                $ret .= $this->_sep.'ĺttio';

                break;
            case 4:
                $ret .= $this->_sep.'fyrtio';

                break;
            case 3:
                $ret .= $this->_sep.'trettio';

                break;
            case 2:
                $ret .= $this->_sep.'tjugo';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'tio';

                        break;
                    case 1:
                        $ret .= $this->_sep.'elva';

                        break;
                    case 2:
                        $ret .= $this->_sep.'tolv';

                        break;
                    case 3:
                        $ret .= $this->_sep.'tretton';

                        break;
                    case 4:
                        $ret .= $this->_sep.'fjorton';

                        break;
                    case 5:
                    case 6:
                        $ret .= $this->_sep.$this->_digits[$d].'ton';

                        break;
                    case 7:
                        $ret .= $this->_sep.'sjutton';

                        break;
                    case 8:
                        $ret .= $this->_sep.'arton';

                        break;
                    case 9:
                        $ret .= $this->_sep.'nitton';
                }

                break;
        }

        if (1 !== $t && $d > 0) { // add digits only in <0>,<1,9> and <21,inf>
            // add minus sign between [2-9] and digit
            $ret .= $this->_sep.$this->_digits[$d];
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
