<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Estonian.
 *
 * @category Numbers
 *
 * @author   Erkki Saarniit <erkki@center.ee>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_et extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'et';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Estonian';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'eesti keel';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'miinus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural)
     * Names partly based on:
     * http://home.earthlink.net/~mrob/pub/math/largenum.html
     * http://mathforum.org/dr.math/faq/faq.large.numbers.html
     * http://www.mazes.com/AmericanNumberingSystem.html.
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['tuhat'],
        6 => ['miljon'],
        9 => ['miljard'],
        12 => ['triljon'],
        15 => ['kvadriljon'],
        18 => ['kvintiljon'],
        21 => ['sekstiljon'],
        24 => ['septiljon'],
        27 => ['oktiljon'],
        30 => ['noniljon'],
        33 => ['dekiljon'],
        36 => ['undekiljon'],
        39 => ['duodekiljon'],
        42 => ['tredekiljon'],
        45 => ['kvattuordekiljon'],
        48 => ['kvindekiljon'],
        51 => ['seksdekiljon'],
        54 => ['septendekiljon'],
        57 => ['oktodekiljon'],
        60 => ['novemdekiljon'],
        63 => ['vigintiljon'],
        66 => ['unvigintiljon'],
        69 => ['duovigintiljon'],
        72 => ['trevigintiljon'],
        75 => ['kvattuorvigintiljon'],
        78 => ['kvinvigintiljon'],
        81 => ['seksvigintiljon'],
        84 => ['septenvigintiljon'],
        87 => ['oktovigintiljon'],
        90 => ['novemvigintiljon'],
        93 => ['trigintiljon'],
        96 => ['untrigintiljon'],
        99 => ['duotrigintiljon'],
        102 => ['trestrigintiljon'],
        105 => ['kvattuortrigintiljon'],
        108 => ['kvintrigintiljon'],
        111 => ['sekstrigintiljon'],
        114 => ['septentrigintiljon'],
        117 => ['oktotrigintiljon'],
        120 => ['novemtrigintiljon'],
        123 => ['kvadragintiljon'],
        126 => ['unkvadragintiljon'],
        129 => ['duokvadragintiljon'],
        132 => ['trekvadragintiljon'],
        135 => ['kvattuorkvadragintiljon'],
        138 => ['kvinkvadragintiljon'],
        141 => ['sekskvadragintiljon'],
        144 => ['septenkvadragintiljon'],
        147 => ['oktokvadragintiljon'],
        150 => ['novemkvadragintiljon'],
        153 => ['kvinkvagintiljon'],
        156 => ['unkvinkvagintiljon'],
        159 => ['duokvinkvagintiljon'],
        162 => ['trekvinkvagintiljon'],
        165 => ['kvattuorkvinkvagintiljon'],
        168 => ['kvinkvinkvagintiljon'],
        171 => ['sekskvinkvagintiljon'],
        174 => ['septenkvinkvagintiljon'],
        177 => ['oktokvinkvagintiljon'],
        180 => ['novemkvinkvagintiljon'],
        183 => ['seksagintiljon'],
        186 => ['unseksagintiljon'],
        189 => ['duoseksagintiljon'],
        192 => ['treseksagintiljon'],
        195 => ['kvattuorseksagintiljon'],
        198 => ['kvinseksagintiljon'],
        201 => ['seksseksagintiljon'],
        204 => ['septenseksagintiljon'],
        207 => ['oktoseksagintiljon'],
        210 => ['novemseksagintiljon'],
        213 => ['septuagintiljon'],
        216 => ['unseptuagintiljon'],
        219 => ['duoseptuagintiljon'],
        222 => ['treseptuagintiljon'],
        225 => ['kvattuorseptuagintiljon'],
        228 => ['kvinseptuagintiljon'],
        231 => ['seksseptuagintiljon'],
        234 => ['septenseptuagintiljon'],
        237 => ['oktoseptuagintiljon'],
        240 => ['novemseptuagintiljon'],
        243 => ['oktogintiljon'],
        246 => ['unoktogintiljon'],
        249 => ['duooktogintiljon'],
        252 => ['treoktogintiljon'],
        255 => ['kvattuoroktogintiljon'],
        258 => ['kvinoktogintiljon'],
        261 => ['seksoktogintiljon'],
        264 => ['septoktogintiljon'],
        267 => ['oktooktogintiljon'],
        270 => ['novemoktogintiljon'],
        273 => ['nonagintiljon'],
        276 => ['unnonagintiljon'],
        279 => ['duononagintiljon'],
        282 => ['trenonagintiljon'],
        285 => ['kvattuornonagintiljon'],
        288 => ['kvinnonagintiljon'],
        291 => ['seksnonagintiljon'],
        294 => ['septennonagintiljon'],
        297 => ['oktononagintiljon'],
        300 => ['novemnonagintiljon'],
        303 => ['kentiljon'],
        309 => ['duokentiljon'],
        312 => ['trekentiljon'],
        366 => ['primo-vigesimo-kentiljon'],
        402 => ['trestrigintakentiljon'],
        603 => ['dukentiljon'],
        624 => ['septendukentiljon'],
        2421 => ['seksoktingentiljon'],
        3003 => ['milliljon'],
        3000003 => ['milli-milliljon'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'null',
        'üks',
        'kaks',
        'kolm',
        'neli',
        'viis',
        'kuus',
        'seitse',
        'kaheksa',
        'üheksa',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = ' ';

    /**
     * Converts a number to its word representation
     * in Estonian language.
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
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num, $power = 0, $powsuffix = '')
    {
        $ret = '';

        if ('-' === substr($num, 0, 1)) {
            $ret = $this->_sep.$this->_minus;
            $num = substr($num, 1);
        }

        $num = trim($num);
        $num = preg_replace('/^0+/', '', $num);

        if (\strlen($num) > 3) {
            $maxp = \strlen($num) - 1;
            $curp = $maxp;

            for ($p = $maxp; $p > 0; --$p) { // power
                if (isset($this->_exponent[$p])) {
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
            $ret .= $this->_sep.$this->_digits[$h].'sada';
        }

        switch ($t) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
            case 4:
            case 3:
            case 2:
                $ret .= $this->_sep.$this->_digits[$t].'kümmend';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'kümme';

                        break;
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $ret .= $this->_sep.$this->_digits[$d].'teist';

                        break;
                }

                break;
        }

        if (1 !== $t && $d > 0) {
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

            if (!isset($lev) || !\is_array($lev)) {
                return null;
            }

            $ret .= $this->_sep.$lev[0].(1 !== $num && 3 !== $power ? 'it' : '');
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep.$powsuffix;
        }

        return $ret;
    }
}
