<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Argentinian Spanish.
 * It supports up to decallones (10^6).
 * It doesn't support spanish tonic accents (acentos).
 *
 * @category Numbers
 *
 * @author   Pavel Oropeza   <pavel@cognus.ath.cx>
 * @author   Martin Marrese  <mmare@mecon.gov.ar>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_es_MX extends Words
{
    // {{{ properties

    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'es_MX';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Spanish';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Español';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'menos';

    /**
     * The sufixes for exponents (singular and plural).
     *
     * @var array
     */
    public $_exponent = [
        0 => ['', ''],
        3 => ['mil', 'mil'],
        6 => ['millón', 'millones'],
        12 => ['billón', 'billones'],
        18 => ['trilón', 'trillones'],
        24 => ['cuatrillón', 'cuatrillones'],
        30 => ['quintillón', 'quintillones'],
        36 => ['sextillón', 'sextillones'],
        42 => ['septillón', 'septillones'],
        48 => ['octallón', 'octallones'],
        54 => ['nonallón', 'nonallones'],
        60 => ['decallón', 'decallones'],
    ];
    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'cero', 'un', 'dos', 'tres', 'cuatro',
        'cinco', 'seis', 'siete', 'ocho', 'nueve',
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
        'ARS' => [['Peso'], ['centavo']],
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
        'GBP' => [['pound', 'pounds'], ['pence']],
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
        'MXN' => [['peso'], ['centavo']],
        'NOK' => [['Norwegian krone'], ['oere']],
        'PLN' => [['zloty', 'zlotys'], ['grosz']],
        'ROL' => [['Romanian leu'], ['bani']],
        'RUB' => [['Russian Federation rouble'], ['kopiejka']],
        'SEK' => [['Swedish krona'], ['oere']],
        'SIT' => [['Tolar'], ['stotinia']],
        'SKK' => [['Slovak koruna'], []],
        'TRL' => [['lira'], ['kuruþ']],
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
    public $def_currency = 'MXN'; // Mexican Peso

    // }}}
    // {{{ _toWords()

    /**
     * Converts a number to its word representation
     * in Mexican Spanish.
     *
     * @param float $num   An float between -infinity and infinity inclusive :)
     *                     that should be converted to a words representation
     * @param int   $power the power of ten for the rest of the number to the right.
     *                     For example toWords(12,3) should give "doce mil".
     *                     Optional, defaults to 0
     *
     * @return string The corresponding word representation
     *
     * @author Martin Marrese
     *
     * @since  Numbers_Words 0.16.3
     */
    public function _toWords($num, $power = 0)
    {
        // The return string;
        $ret = '';

        // add a the word for the minus sign if necessary
        if ('-' === substr($num, 0, 1)) {
            $ret = $this->_sep.$this->_minus;
            $num = substr($num, 1);
        }

        // strip excessive zero signs
        $num = preg_replace('/^0+/', '', $num);

        $num_tmp = explode('.', $num);

        $num = $num_tmp[0];
        $dec = (@$num_tmp[1]) ? $num_tmp[1] : '';

        if (\strlen($num) > 6) {
            $current_power = 6;
            // check for highest power
            if (isset($this->_exponent[$power])) {
                // convert the number above the first 6 digits
                // with it's corresponding $power.
                $snum = substr($num, 0, -6);
                $snum = preg_replace('/^0+/', '', $snum);
                if ('' !== $snum) {
                    $ret .= $this->_toWords($snum, $power + 6);
                }
            }
            $num = substr($num, -6);
            if (0 === $num) {
                return $ret;
            }
        } elseif (0 === $num || '' === $num) {
            return ' '.$this->_digits[0];
            $current_power = \strlen($num);
        } else {
            $current_power = \strlen($num);
        }

        // See if we need "thousands"
        $thousands = floor($num / 1000);
        if (1 === $thousands) {
            $ret .= $this->_sep.'mil';
        } elseif ($thousands > 1) {
            $ret .= $this->_toWords($thousands, 3);
        }

        // values for digits, tens and hundreds
        $h = floor(($num / 100) % 10);
        $t = floor(($num / 10) % 10);
        $d = floor($num % 10);

        // cientos: doscientos, trescientos, etc...
        switch ($h) {
            case 1:
                if ((0 === $d) && (0 === $t)) { // is it's '100' use 'cien'
                    $ret .= $this->_sep.'cien';
                } else {
                    $ret .= $this->_sep.'ciento';
                }

                break;
            case 2:
            case 3:
            case 4:
            case 6:
            case 8:
                $ret .= $this->_sep.$this->_digits[$h].'cientos';

                break;
            case 5:
                $ret .= $this->_sep.'quinientos';

                break;
            case 7:
                $ret .= $this->_sep.'setecientos';

                break;
            case 9:
                $ret .= $this->_sep.'novecientos';

                break;
        }

        // decenas: veinte, treinta, etc...
        switch ($t) {
            case 9:
                $ret .= $this->_sep.'noventa';

                break;
            case 8:
                $ret .= $this->_sep.'ochenta';

                break;
            case 7:
                $ret .= $this->_sep.'setenta';

                break;
            case 6:
                $ret .= $this->_sep.'sesenta';

                break;
            case 5:
                $ret .= $this->_sep.'cincuenta';

                break;
            case 4:
                $ret .= $this->_sep.'cuarenta';

                break;
            case 3:
                $ret .= $this->_sep.'treinta';

                break;
            case 2:
                if (0 === $d) {
                    $ret .= $this->_sep.'veinte';
                } else {
                    if (($power > 0) && (1 === $d)) {
                        $ret .= $this->_sep.'veintiún';
                    } else {
                        $ret .= $this->_sep.'veinti'.$this->_digits[$d];
                    }
                }

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'diez';

                        break;
                    case 1:
                        $ret .= $this->_sep.'once';

                        break;
                    case 2:
                        $ret .= $this->_sep.'doce';

                        break;
                    case 3:
                        $ret .= $this->_sep.'trece';

                        break;
                    case 4:
                        $ret .= $this->_sep.'catorce';

                        break;
                    case 5:
                        $ret .= $this->_sep.'quince';

                        break;
                    case 6:
                    case 7:
                    case 9:
                    case 8:
                        $ret .= $this->_sep.'dieci'.$this->_digits[$d];

                        break;
                }

                break;
        }

        // add digits only if it is a multiple of 10 and not 1x or 2x
        if ((1 !== $t) && (2 !== $t) && ($d > 0)) {
            // don't add 'y' for numbers below 10
            if (0 !== $t) {
                // use 'un' instead of 'uno' when there is a suffix ('mil', 'millones', etc...)
                if (($power > 0) && (1 === $d)) {
                    $ret .= $this->_sep.' y un';
                } else {
                    $ret .= $this->_sep.'y '.$this->_digits[$d];
                }
            } else {
                if (($power > 0) && (1 === $d)) {
                    $ret .= $this->_sep.'un';
                } else {
                    $ret .= $this->_sep.$this->_digits[$d];
                }
            }
        }

        if ($power > 0) {
            if (isset($this->_exponent[$power])) {
                $lev = $this->_exponent[$power];
            }

            if (!isset($lev) || !\is_array($lev)) {
                return null;
            }

            // if it's only one use the singular suffix
            if ((1 === $d) && (0 === $t) && (0 === $h)) {
                $suffix = $lev[0];
            } else {
                $suffix = $lev[1];
            }
            if (0 !== $num) {
                $ret .= $this->_sep.$suffix;
            }
        }

        if ($dec) {
            $dec = $this->_toWords(trim($dec));
            $ret .= ' con '.trim($dec);
        }

        return $ret;
    }

    /**
     * Converts a currency value to its word representation
     * (with monetary units) in Mexican Spanish language.
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
     * @author Pavel Oropeza
     */
    public function toCurrencyWords($int_curr, $decimal, $fraction = false, $convert_fraction = true)
    {
        if (!isset($this->_currency_names[$int_curr])) {
            $int_curr = $this->def_currency;
        }

        $curr_names = $this->_currency_names[$int_curr];

        $lev = (1 === $decimal) ? 0 : 1;
        if ($lev > 0) {
            $curr_names = $this->_currency_names[$int_curr];
            if (\count($curr_names[0]) > 1) {
                $ret = $curr_names[0][$lev];
            } else {
                $ret = $curr_names[0][0].'s';
            }
        } else {
            $ret = $curr_names[0][0];
        }
        $ret = $this->_sep.ucfirst(trim($this->_toWords($decimal).' '.$ret));

        if (false !== $fraction) {
            if ($convert_fraction) {
                $ret .= $this->_sep.'con'.$this->_sep.trim($this->_toWords($fraction));
            } else {
                $ret .= $this->_sep.'con'.$this->_sep.$fraction;
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
