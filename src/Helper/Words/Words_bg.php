<?php

namespace Hgabka\UtilsBundle\Helper\Words;

use Hgabka\UtilsBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Bulgarian.
 *
 * @category Numbers
 *
 * @author   Kouber Saparev <kouber@php.net>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_bg extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'bg';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Bulgarian';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Български';

    /**
     * Some miscellaneous words and language constructs.
     *
     * @var string
     */
    public $_misc_strings = [
        'deset' => 'десет',           // "ten"
        'edinadeset' => 'единадесет', // "eleven"
        'na' => 'на',                 // liaison particle for 12 to 19
        'sto' => 'сто',               // "hundred"
        'sta' => 'ста',               // suffix for 2 and 3 hundred
        'stotin' => 'стотин',         // suffix for 4 to 9 hundred
        'hiliadi' => 'хиляди',         // plural form of "thousand"
    ];

    /**
     * The words for digits (except zero). Note that, there are three genders for them (neuter, masculine and feminine).
     * The words for 3 to 9 (masculine) and for 2 to 9 (feminine) are the same as neuter, so they're filled
     * in the _initDigits() method, which is invoked from the constructor.
     *
     * @var string
     */
    public $_digits = [
        0 => [1 => 'едно', 'две', 'три', 'четири', 'пет', 'шест', 'седем', 'осем', 'девет'], // neuter
        1 => [1 => 'един', 'два'],                                                           // masculine
        -1 => [1 => 'една'],                                                                   // feminine
    ];

    /**
     * A flag, that determines if the _digits array is filled for masculine and feminine genders.
     *
     * @var string
     */
    public $_digits_initialized = false;

    /**
     * A flag, that determines if the "and" word is placed already before the last non-empty group of digits.
     *
     * @var string
     */
    public $_last_and = false;

    /**
     * The word for zero.
     *
     * @var string
     */
    public $_zero = 'нула';

    /**
     * The word for infinity.
     *
     * @var string
     */
    public $_infinity = 'безкрайност';

    /**
     * The word for the "and" language construct.
     *
     * @var string
     */
    public $_and = 'и';

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = ' ';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'минус'; // minus sign

    /**
     * The plural suffix (except for thousand).
     *
     * @var string
     */
    public $_plural = 'а'; // plural suffix

    /**
     * The suffixes for exponents (singular).
     *
     * @var array
     */
    public $_exponent = [
        0 => '',
        3 => 'хиляда',
        6 => 'милион',
        9 => 'милиард',
        12 => 'трилион',
        15 => 'квадрилион',
        18 => 'квинтилион',
        21 => 'секстилион',
        24 => 'септилион',
        27 => 'октилион',
        30 => 'ноналион',
        33 => 'декалион',
        36 => 'ундекалион',
        39 => 'дуодекалион',
        42 => 'тредекалион',
        45 => 'кватордекалион',
        48 => 'квинтдекалион',
        51 => 'сексдекалион',
        54 => 'септдекалион',
        57 => 'октодекалион',
        60 => 'новемдекалион',
        63 => 'вигинтилион',
        66 => 'унвигинтилион',
        69 => 'дуовигинтилион',
        72 => 'тревигинтилион',
        75 => 'кваторвигинтилион',
        78 => 'квинвигинтилион',
        81 => 'сексвигинтилион',
        84 => 'септенвигинтилион',
        87 => 'октовигинтилион',
        90 => 'новемвигинтилион',
        93 => 'тригинтилион',
        96 => 'унтригинтилион',
        99 => 'дуотригинтилион',
        102 => 'третригинтилион',
        105 => 'кватортригинтилион',
        108 => 'квинтригинтилион',
        111 => 'секстригинтилион',
        114 => 'септентригинтилион',
        117 => 'октотригинтилион',
        120 => 'новемтригинтилион',
        123 => 'квадрагинтилион',
        126 => 'унквадрагинтилион',
        129 => 'дуоквадрагинтилион',
        132 => 'треквадрагинтилион',
        135 => 'кваторквадрагинтилион',
        138 => 'квинквадрагинтилион',
        141 => 'сексквадрагинтилион',
        144 => 'септенквадрагинтилион',
        147 => 'октоквадрагинтилион',
        150 => 'новемквадрагинтилион',
        153 => 'квинквагинтилион',
        156 => 'унквинкагинтилион',
        159 => 'дуоквинкагинтилион',
        162 => 'треквинкагинтилион',
        165 => 'кваторквинкагинтилион',
        168 => 'квинквинкагинтилион',
        171 => 'сексквинкагинтилион',
        174 => 'септенквинкагинтилион',
        177 => 'октоквинкагинтилион',
        180 => 'новемквинкагинтилион',
        183 => 'сексагинтилион',
        186 => 'унсексагинтилион',
        189 => 'дуосексагинтилион',
        192 => 'тресексагинтилион',
        195 => 'кваторсексагинтилион',
        198 => 'квинсексагинтилион',
        201 => 'секссексагинтилион',
        204 => 'септенсексагинтилион',
        207 => 'октосексагинтилион',
        210 => 'новемсексагинтилион',
        213 => 'септагинтилион',
        216 => 'унсептагинтилион',
        219 => 'дуосептагинтилион',
        222 => 'тресептагинтилион',
        225 => 'кваторсептагинтилион',
        228 => 'квинсептагинтилион',
        231 => 'секссептагинтилион',
        234 => 'септенсептагинтилион',
        237 => 'октосептагинтилион',
        240 => 'новемсептагинтилион',
        243 => 'октогинтилион',
        246 => 'уноктогинтилион',
        249 => 'дуооктогинтилион',
        252 => 'треоктогинтилион',
        255 => 'кватороктогинтилион',
        258 => 'квиноктогинтилион',
        261 => 'сексоктогинтилион',
        264 => 'септоктогинтилион',
        267 => 'октооктогинтилион',
        270 => 'новемоктогинтилион',
        273 => 'нонагинтилион',
        276 => 'уннонагинтилион',
        279 => 'дуононагинтилион',
        282 => 'тренонагинтилион',
        285 => 'кваторнонагинтилион',
        288 => 'квиннонагинтилион',
        291 => 'секснонагинтилион',
        294 => 'септеннонагинтилион',
        297 => 'октононагинтилион',
        300 => 'новемнонагинтилион',
        303 => 'центилион',
    ];

    /**
     * The class constructor, used for calling the _initDigits method.
     *
     * @author Kouber Saparev <kouber@php.net>
     *
     * @see function _initDigits
     */
    public function Numbers_Words_bg()
    {
        $this->_initDigits();
    }

    /**
     * Fills the _digits array for masculine and feminine genders with
     * corresponding references to neuter words (when they're the same).
     *
     * @author Kouber Saparev <kouber@php.net>
     */
    public function _initDigits()
    {
        if (!$this->_digits_initialized) {
            for ($i = 3; $i <= 9; ++$i) {
                $this->_digits[1][$i] = &$this->_digits[0][$i];
            }
            for ($i = 2; $i <= 9; ++$i) {
                $this->_digits[-1][$i] = &$this->_digits[0][$i];
            }
            $this->_digits_initialized = true;
        }
    }

    /**
     * Split a number to groups of three-digit numbers.
     *
     * @param mixed $num An integer or its string representation
     *                   that need to be split
     *
     * @return array groups of three-digit numbers
     *
     * @author Kouber Saparev <kouber@php.net>
     *
     * @since  PHP 4.2.3
     */
    public function _splitNumber($num)
    {
        if (\is_string($num)) {
            $ret = [];

            $strlen = \strlen($num);
            $first = substr($num, 0, $strlen % 3);

            preg_match_all('/\d{3}/', substr($num, $strlen % 3, $strlen), $m);

            $ret = &$m[0];
            if ($first) {
                array_unshift($ret, $first);
            }

            return $ret;
        }

        return explode(' ', number_format($num, 0, '', ' ')); // a faster version for integers
    }

    /**
     * Converts a three-digit number to its word representation
     * in Bulgarian language.
     *
     * @param int  $num    an integer between 1 and 999 inclusive
     * @param int  $gender an integer which represents the gender of
     *                     the current digits group.
     *                     0 - neuter
     *                     1 - masculine
     *                     -1 - feminine
     * @param bool $last   a flag that determines if the current digits group
     *                     is the last one
     *
     * @return string the words for the given number
     *
     * @author Kouber Saparev <kouber@php.net>
     */
    public function _showDigitsGroup($num, $gender = 0, $last = false)
    {
        /* A storage array for the return string.
             Positions 1, 3, 5 are intended for digit words
             and everything else (0, 2, 4) for "and" words.
             Both of the above types are optional, so the size of
             the array may vary.
        */
        $ret = [];

        // extract the value of each digit from the three-digit number
        $e = $num % 10;                  // ones
        $d = ($num - $e) % 100 / 10;         // tens
        $s = ($num - $d * 10 - $e) % 1000 / 100; // hundreds

        // process the "hundreds" digit.
        if ($s) {
            switch ($s) {
                case 1:
                    $ret[1] = $this->_misc_strings['sto'];

                    break;
                case 2:
                case 3:
                    $ret[1] = $this->_digits[0][$s] . $this->_misc_strings['sta'];

                    break;
                default:
                    $ret[1] = $this->_digits[0][$s] . $this->_misc_strings['stotin'];
            }
        }

        // process the "tens" digit, and optionally the "ones" digit.
        if ($d) {
            // in the case of 1, the "ones" digit also must be processed
            if (1 === $d) {
                if (!$e) {
                    $ret[3] = $this->_misc_strings['deset']; // ten
                } else {
                    if (1 === $e) {
                        $ret[3] = $this->_misc_strings['edinadeset']; // eleven
                    } else {
                        $ret[3] = $this->_digits[1][$e] . $this->_misc_strings['na'] . $this->_misc_strings['deset']; // twelve - nineteen
                    }
                    // the "ones" digit is alredy processed, so skip a second processment
                    $e = 0;
                }
            } else {
                $ret[3] = $this->_digits[1][$d] . $this->_misc_strings['deset']; // twenty - ninety
            }
        }

        // process the "ones" digit
        if ($e) {
            $ret[5] = $this->_digits[$gender][$e];
        }

        // put "and" where needed
        if (\count($ret) > 1) {
            if ($e) {
                $ret[4] = $this->_and;
            } else {
                $ret[2] = $this->_and;
            }
        }

        // put "and" optionally in the case this is the last non-empty group
        if ($last) {
            if (!$s || 1 === \count($ret)) {
                $ret[0] = $this->_and;
            }
            $this->_last_and = true;
        }

        // sort the return array so that "and" constructs go to theirs appropriate places
        ksort($ret);

        return implode($this->_sep, $ret);
    }

    /**
     * Converts a number to its word representation
     * in Bulgarian language.
     *
     * @param int $num An integer between 9.99*-10^302 and 9.99*10^302 (999 centillions)
     *                 that need to be converted to words
     *
     * @return string The corresponding word representation
     *
     * @author Kouber Saparev <kouber@php.net>
     *
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num = 0)
    {
        $ret = [];

        $ret_minus = '';

        // check if $num is a valid non-zero number
        if (!$num || preg_match('/^-?0+$/', $num) || !preg_match('/^-?\d+$/', $num)) {
            return $this->_zero;
        }

        // add a minus sign
        if ('-' === substr($num, 0, 1)) {
            $ret_minus = $this->_minus . $this->_sep;

            $num = substr($num, 1);
        }

        // if the absolute value is greater than 9.99*10^302, return infinity
        if (\strlen($num) > 306) {
            return $ret_minus . $this->_infinity;
        }

        // strip excessive zero signs
        $num = ltrim($num, '0');

        // split $num to groups of three-digit numbers
        $num_groups = $this->_splitNumber($num);

        $sizeof_numgroups = \count($num_groups);

        // go through the groups in reverse order, so that the last group could be determined
        for ($i = $sizeof_numgroups - 1, $j = 1; $i >= 0; $i--, $j++) {
            if (!isset($ret[$j])) {
                $ret[$j] = '';
            }

            // what is the corresponding exponent for the current group
            $pow = $sizeof_numgroups - $i;

            // skip processment for empty groups
            if ('000' !== $num_groups[$i]) {
                if ($num_groups[$i] > 1) {
                    if (1 === $pow) {
                        $ret[$j] .= $this->_showDigitsGroup($num_groups[$i], 0, !$this->_last_and && $i) . $this->_sep;
                        $ret[$j] .= $this->_exponent[($pow - 1) * 3];
                    } elseif (2 === $pow) {
                        $ret[$j] .= $this->_showDigitsGroup($num_groups[$i], -1, !$this->_last_and && $i) . $this->_sep;
                        $ret[$j] .= $this->_misc_strings['hiliadi'] . $this->_sep;
                    } else {
                        $ret[$j] .= $this->_showDigitsGroup($num_groups[$i], 1, !$this->_last_and && $i) . $this->_sep;
                        $ret[$j] .= $this->_exponent[($pow - 1) * 3] . $this->_plural . $this->_sep;
                    }
                } else {
                    if (1 === $pow) {
                        $ret[$j] .= $this->_showDigitsGroup($num_groups[$i], 0, !$this->_last_and && $i) . $this->_sep;
                    } elseif (2 === $pow) {
                        $ret[$j] .= $this->_exponent[($pow - 1) * 3] . $this->_sep;
                    } else {
                        $ret[$j] .= $this->_digits[1][1] . $this->_sep . $this->_exponent[($pow - 1) * 3] . $this->_sep;
                    }
                }
            }
        }

        return $ret_minus . rtrim(implode('', array_reverse($ret)), $this->_sep);
    }
}
