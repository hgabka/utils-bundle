<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Hungarian.
 *
 * @category Numbers
 *
 * @author   Nils Homp
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_hu extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'hu';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Hungarian';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Magyar';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'Mínusz '; // minus sign

    /**
     * The suffixes for exponents (singular and plural)
     * Names based on:
     * http://mek.oszk.hu/adatbazis/lexikon/phplex/lexikon/d/kisokos/186.html.
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['ezer'],
        6 => ['millió'],
        9 => ['milliárd'],
        12 => ['billió'],
        15 => ['billiárd'],
        18 => ['trillió'],
        21 => ['trilliárd'],
        24 => ['kvadrillió'],
        27 => ['kvadrilliárd'],
        30 => ['kvintillió'],
        33 => ['kvintilliárd'],
        36 => ['szextillió'],
        39 => ['szextilliárd'],
        42 => ['szeptillió'],
        45 => ['szeptilliárd'],
        48 => ['oktillió'],
        51 => ['oktilliárd'],
        54 => ['nonillió'],
        57 => ['nonilliárd'],
        60 => ['decillió'],
        63 => ['decilliárd'],
        600 => ['centillió'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'nulla', 'egy', 'kettő', 'három', 'négy',
        'öt', 'hat', 'hét', 'nyolc', 'kilenc',
    ];

    /**
     * The word separator.
     *
     * @var string
     */
    public $_sep = '';

    /**
     * The thousands word separator.
     *
     * @var string
     */
    public $_thousand_sep = '-';

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
    public $def_currency = 'HUF'; // forint

    /**
     * Converts a number to its word representation
     * in the Hungarian language.
     *
     * @param int   $num       An integer between -infinity and infinity inclusive :)
     *                         that need to be converted to words
     * @param int   $power     the power of ten for the rest of the number to the right.
     *                         Optional, defaults to 0
     * @param int   $powsuffix The power name to be added to the end of the return string.
     *                         Used internally. Optional, defaults to ''.
     * @param mixed $options
     * @param mixed $gt2000
     *
     * @return string The corresponding word representation
     *
     * @author Nils Homp
     *
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num, $options = [], $power = 0, $powsuffix = '', $gt2000 = false)
    {
        $chk_gt2000 = true;

        // Loads user options
        extract($options, \EXTR_IF_EXISTS);

        /**
         * Return string.
         */
        $ret = '';

        // add a minus sign
        if ('-' === substr($num, 0, 1)) {
            $ret = $this->_sep.$this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs and spaces
        $num = trim($num);
        $num = preg_replace('/^0+/', '', $num);

        if ($chk_gt2000) {
            $gt2000 = $num > 2000;
        }

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

                        $ret .= $this->_toWords(
                            $snum,
                            ['chk_gt2000' => false],
                            $p,
                            $cursuffix,
                            $gt2000
                        );

                        if ($gt2000) {
                            $ret .= $this->_thousand_sep;
                        }
                    }
                    $curp = $p - 1;

                    continue;
                }
            }
            $num = substr($num, $maxp - $curp, $curp - $p + 1);
            if (0 === $num) {
                return rtrim($ret, $this->_thousand_sep);
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
            $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'száz';
        }

        // ten, twenty etc.
        switch ($t) {
            case 9:
            case 5:
                $ret .= $this->_sep.$this->_digits[$t].'ven';

                break;
            case 8:
            case 6:
                $ret .= $this->_sep.$this->_digits[$t].'van';

                break;
            case 7:
                $ret .= $this->_sep.'hetven';

                break;
            case 3:
                $ret .= $this->_sep.'harminc';

                break;
            case 4:
                $ret .= $this->_sep.'negyven';

                break;
            case 2:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'húsz';

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
                        $ret .= $this->_sep.'húszon';

                        break;
                }

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'tíz';

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
                        $ret .= $this->_sep.'tizen';

                        break;
                }

                break;
        }

        if ($d > 0) { // add digits only in <0> and <1,inf)
            $ret .= $this->_sep.$this->_digits[$d];
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !\is_array($lev)) {
                return null;
            }

            $ret .= $this->_sep.$lev[0];
        }

        if ('' !== $powsuffix) {
            $ret .= $this->_sep.$powsuffix;
        }

        return $ret;
    }

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
            if (\count($curr_names[0]) > 1) {
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
                if (\count($curr_names[1]) > 1) {
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
}
