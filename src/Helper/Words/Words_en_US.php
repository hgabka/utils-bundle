<?php

namespace Hgabka\UtilsBundle\Helper\Words;

use Hgabka\UtilsBundle\Helper\Number\Words;

/**
 * Class for translating numbers into American English.
 *
 * @category Numbers
 *
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_en_US extends Words
{
    // {{{ properties

    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'en_US';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'American English';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'American English';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'minus'; // minus sign

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
        3 => ['thousand'],
        6 => ['million'],
        9 => ['billion'],
       12 => ['trillion'],
       15 => ['quadrillion'],
       18 => ['quintillion'],
       21 => ['sextillion'],
       24 => ['septillion'],
       27 => ['octillion'],
       30 => ['nonillion'],
       33 => ['decillion'],
       36 => ['undecillion'],
       39 => ['duodecillion'],
       42 => ['tredecillion'],
       45 => ['quattuordecillion'],
       48 => ['quindecillion'],
       51 => ['sexdecillion'],
       54 => ['septendecillion'],
       57 => ['octodecillion'],
       60 => ['novemdecillion'],
       63 => ['vigintillion'],
       66 => ['unvigintillion'],
       69 => ['duovigintillion'],
       72 => ['trevigintillion'],
       75 => ['quattuorvigintillion'],
       78 => ['quinvigintillion'],
       81 => ['sexvigintillion'],
       84 => ['septenvigintillion'],
       87 => ['octovigintillion'],
       90 => ['novemvigintillion'],
       93 => ['trigintillion'],
       96 => ['untrigintillion'],
       99 => ['duotrigintillion'],
       // 100 => array('googol') - not latin name
       // 10^googol = 1 googolplex
      102 => ['trestrigintillion'],
      105 => ['quattuortrigintillion'],
      108 => ['quintrigintillion'],
      111 => ['sextrigintillion'],
      114 => ['septentrigintillion'],
      117 => ['octotrigintillion'],
      120 => ['novemtrigintillion'],
      123 => ['quadragintillion'],
      126 => ['unquadragintillion'],
      129 => ['duoquadragintillion'],
      132 => ['trequadragintillion'],
      135 => ['quattuorquadragintillion'],
      138 => ['quinquadragintillion'],
      141 => ['sexquadragintillion'],
      144 => ['septenquadragintillion'],
      147 => ['octoquadragintillion'],
      150 => ['novemquadragintillion'],
      153 => ['quinquagintillion'],
      156 => ['unquinquagintillion'],
      159 => ['duoquinquagintillion'],
      162 => ['trequinquagintillion'],
      165 => ['quattuorquinquagintillion'],
      168 => ['quinquinquagintillion'],
      171 => ['sexquinquagintillion'],
      174 => ['septenquinquagintillion'],
      177 => ['octoquinquagintillion'],
      180 => ['novemquinquagintillion'],
      183 => ['sexagintillion'],
      186 => ['unsexagintillion'],
      189 => ['duosexagintillion'],
      192 => ['tresexagintillion'],
      195 => ['quattuorsexagintillion'],
      198 => ['quinsexagintillion'],
      201 => ['sexsexagintillion'],
      204 => ['septensexagintillion'],
      207 => ['octosexagintillion'],
      210 => ['novemsexagintillion'],
      213 => ['septuagintillion'],
      216 => ['unseptuagintillion'],
      219 => ['duoseptuagintillion'],
      222 => ['treseptuagintillion'],
      225 => ['quattuorseptuagintillion'],
      228 => ['quinseptuagintillion'],
      231 => ['sexseptuagintillion'],
      234 => ['septenseptuagintillion'],
      237 => ['octoseptuagintillion'],
      240 => ['novemseptuagintillion'],
      243 => ['octogintillion'],
      246 => ['unoctogintillion'],
      249 => ['duooctogintillion'],
      252 => ['treoctogintillion'],
      255 => ['quattuoroctogintillion'],
      258 => ['quinoctogintillion'],
      261 => ['sexoctogintillion'],
      264 => ['septoctogintillion'],
      267 => ['octooctogintillion'],
      270 => ['novemoctogintillion'],
      273 => ['nonagintillion'],
      276 => ['unnonagintillion'],
      279 => ['duononagintillion'],
      282 => ['trenonagintillion'],
      285 => ['quattuornonagintillion'],
      288 => ['quinnonagintillion'],
      291 => ['sexnonagintillion'],
      294 => ['septennonagintillion'],
      297 => ['octononagintillion'],
      300 => ['novemnonagintillion'],
      303 => ['centillion'],
      309 => ['duocentillion'],
      312 => ['trecentillion'],
      366 => ['primo-vigesimo-centillion'],
      402 => ['trestrigintacentillion'],
      603 => ['ducentillion'],
      624 => ['septenducentillion'],
     // bug on a earthlink page: 903 => array('trecentillion'),
     2421 => ['sexoctingentillion'],
     3003 => ['millillion'],
     3000003 => ['milli-millillion'],
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

    /**
     * The currency names (based on the below links,
     * informations from central bank websites and on encyclopedias).
     *
     * @var array
     *
     * @see http://30-03-67.dreamstation.com/currency_alfa.htm World Currency Information
     * @see http://www.jhall.demon.co.uk/currency/by_abbrev.html World currencies
     * @see http://www.shoestring.co.kr/world/p.visa/change.htm Currency names in English
     */
    public $_currency_names = [
      'ALL' => [['lek'], ['qindarka']],
      'AUD' => [['Australian dollar'], ['cent']],
      'BAM' => [['convertible marka'], ['fenig']],
      'BGN' => [['lev'], ['stotinka']],
      'BRL' => [['real'], ['centavos']],
      'BYR' => [['Belarussian rouble'], ['kopiejka']],
      'CAD' => [['Canadian dollar'], ['cent']],
      'CHF' => [['Swiss franc'], ['rapp']],
      'CYP' => [['Cypriot pound'], ['cent']],
      'CZK' => [['Czech koruna'], ['halerz']],
      'DKK' => [['Danish krone'], ['ore']],
      'EEK' => [['kroon'], ['senti']],
      'EUR' => [['euro'], ['euro-cent']],
      'GBP' => [['pound', 'pounds'], ['pence', 'pence']],
      'HKD' => [['Hong Kong dollar'], ['cent']],
      'HRK' => [['Croatian kuna'], ['lipa']],
      'HUF' => [['forint'], ['filler']],
      'ILS' => [['new sheqel', 'new sheqels'], ['agora', 'agorot']],
      'ISK' => [['Icelandic króna'], ['aurar']],
      'JPY' => [['yen'], ['sen']],
      'LTL' => [['litas'], ['cent']],
      'LVL' => [['lat'], ['sentim']],
      'MKD' => [['Macedonian dinar'], ['deni']],
      'MTL' => [['Maltese lira'], ['centym']],
      'NOK' => [['Norwegian krone'], ['oere']],
      'PLN' => [['zloty', 'zlotys'], ['grosz']],
      'ROL' => [['Romanian leu'], ['bani']],
      'RUB' => [['Russian Federation rouble'], ['kopiejka']],
      'SEK' => [['Swedish krona'], ['oere']],
      'SIT' => [['Tolar'], ['stotinia']],
      'SKK' => [['Slovak koruna'], []],
      'TRL' => [['lira'], ['kuruţ']],
      'UAH' => [['hryvna'], ['cent']],
      'USD' => [['dollar'], ['cent']],
      'YUM' => [['dinars'], ['para']],
      'ZAR' => [['rand'], ['cent']],
    ];

    /**
     * The default currency name.
     *
     * @var string
     */
    public $def_currency = 'USD'; // American dollar

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in American English language.
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

    // }}}
    // {{{ toCurrencyWords()

    /**
     * Converts a currency value to its word representation
     * (with monetary units) in English language.
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
     * @author Piotr Klaban <makler@man.torun.pl>
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

        $ret = trim($this->_toWords($decimal));
        $lev = (1 === $decimal) ? 0 : 1;
        if ($lev > 0) {
            if (count($curr_names[0]) > 1) {
                $ret .= $this->_sep.$curr_names[0][$lev];
            } else {
                $ret .= $this->_sep.$curr_names[0][0].'s';
            }
        } else {
            $ret .= $this->_sep.$curr_names[0][0];
        }

        if (false !== $fraction) {
            if ($convert_fraction) {
                $ret .= $this->_sep.trim($this->_toWords($fraction));
            } else {
                $ret .= $this->_sep.$fraction;
            }
            $lev = (1 === $fraction) ? 0 : 1;
            if ($lev > 0) {
                if (count($curr_names[1]) > 1) {
                    $ret .= $this->_sep.$curr_names[1][$lev];
                } else {
                    $ret .= $this->_sep.$curr_names[1][0].'s';
                }
            } else {
                $ret .= $this->_sep.$curr_names[1][0];
            }
        }

        return $ret;
    }

    // }}}
}
