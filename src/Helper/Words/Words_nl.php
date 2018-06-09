<?php

namespace Hgabka\KunstmaanExtensionBundle\Helper\Words;

use Hgabka\KunstmaanExtensionBundle\Helper\Number\Words;

/**
 * Class for translating numbers into Dutch.
 *
 * @category Numbers
 *
 * @author   Piotr Klaban <makler@man.torun.pl>
 * @author   WHAM van Dinter (for Dutch Translations)
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
 *
 * @see     http://pear.php.net/package/Numbers_Words
 */
class Words_nl extends Words
{
    /**
     * Locale name.
     *
     * @var string
     */
    public $locale = 'nl';

    /**
     * Language name in English.
     *
     * @var string
     */
    public $lang = 'Dutch';

    /**
     * Native language name.
     *
     * @var string
     */
    public $lang_native = 'Nederlands';

    /**
     * The word for the minus sign.
     *
     * @var string
     */
    public $_minus = 'Minus'; // minus sign

    /**
     * The sufixes for exponents (singular and plural)
     * Names partly based on:
     * http://nl.wikipedia.org/wiki/Quadriljoen.
     *
     * @var array
     */
    public $_exponent = [
        0 => [''],
        3 => ['Duizend', 'Duizend'],
        6 => ['Miljoen', 'Miljoen'],
        9 => ['Miljard', 'Miljard'],
        12 => ['Biljoen', 'Biljoen'],
        15 => ['Biljard', 'Biljard'],
        18 => ['Triljoen', 'Triljoen'],
        21 => ['Triljard', 'Triljard'],
        24 => ['Quadriljoen', 'Quadriljoen'],
        27 => ['Quadriljard', 'Quadriljard'],
        30 => ['Quintiljoen', 'Quintiljoen'],
        33 => ['Quintiljard', 'Quintiljard'],
        36 => ['Sextiljoen', 'Sextiljoen'],
        39 => ['Sextiljard', 'Sextiljard'],
        42 => ['Septiljoen', 'Septiljoen'],
        45 => ['Septiljard', 'Septiljard'],
        48 => ['Octiljoen', 'Octiljoen'],
        51 => ['Octiljard', 'Octiljard'],
        54 => ['Noniljoen', 'Noniljoen'],
        57 => ['Noniljard', 'Noniljard'],
        60 => ['Deciljoen', 'Deciljoen'],
        63 => ['Deciljard', 'Deciljard'],
        66 => ['Undeciljoen', 'Undeciljoen'],
        69 => ['Undeciljard', 'Undeciljard'],
        72 => ['Duodeciljoen', 'Duodeciljoen'],
        75 => ['Duodeciljard', 'Duodeciljard'],
        78 => ['Tredeciljoen', 'Tredeciljoen'],
        81 => ['Tredeciljard', 'Tredeciljard'],
        120 => ['Vigintiljoen', 'Vigintiljoen'],
        123 => ['Vigintiljard', 'Vigintiljard'],
        600 => ['Zentiljoen', 'Zentiljoen'], // oder Centillion
        603 => ['Zentiljardn', 'Zentiljard'],
    ];

    /**
     * The array containing the digits (indexed by the digits themselves).
     *
     * @var array
     */
    public $_digits = [
        0 => 'nul', 'een', 'twee', 'drie', 'vier',
        'vijf', 'zes', 'zeven', 'acht', 'negen',
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
    public $_sep2 = '-';

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
        'ISK' => [['Icelandic kr\F3na'], ['aurar']],
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
        'TRL' => [['lira'], ['kuru\FE']],
        'UAH' => [['hryvna'], ['cent']],
        'USD' => [['dollar'], ['cent']],
        'YUM' => [['dinars'], ['para']],
        'ZAR' => [['rand'], ['cent']],
    ];

    public $def_currency = 'EUR';

    /**
     * Converts a number to its word representation
     * in Dutch language.
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
     * @author WHAM van Dinter <willem@fkkc.nl>
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
            $ret .= $this->_sep.$this->_digits[$h].$this->_sep.'honderd';
        }

        // add digits only in <0>,<1,9> and <21,inf>
        if (1 !== $t && $d > 0) {
            if ($t > 0) {
                $ret .= $this->_digits[$d].'en';
            } else {
                $ret .= $this->_digits[$d];
                if (1 === $d) {
                    if (0 === $power) {
                        $ret .= 's'; // fuer eins
                    } else {
                        if (3 !== $power) {  // tausend ausnehmen
                            $ret .= ''; // fuer eine
                        }
                    }
                }
            }
        }

        // ten, twenty etc.
        switch ($t) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
                $ret .= $this->_sep.$this->_digits[$t].'tig';

                break;
            case 4:
                $ret .= $this->_sep.'veertig';

                break;
            case 3:
                $ret .= $this->_sep.'dertig';

                break;
            case 2:
                $ret .= $this->_sep.'twintig';

                break;
            case 1:
                switch ($d) {
                    case 0:
                        $ret .= $this->_sep.'tien';

                        break;
                    case 1:
                        $ret .= $this->_sep.'elf';

                        break;
                    case 2:
                        $ret .= $this->_sep.'twaalf';

                        break;
                    case 3:
                        $ret .= $this->_sep.'dertien';

                        break;
                    case 4:
                        $ret .= $this->_sep.'veertien';

                        break;
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $ret .= $this->_sep.$this->_digits[$d].'tien';

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
}
