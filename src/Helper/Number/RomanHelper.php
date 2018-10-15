<?php

namespace Hgabka\UtilsBundle\Helper\Number;

class RomanHelper
{
    //array of roman values
    protected $romanValues = [
        'I' => 1,
        'V' => 5,
        'X' => 10,
        'L' => 50,
        'C' => 100,
        'D' => 500,
        'M' => 1000,
    ];

    //values that should evaluate as 0
    protected $romanZero = ['N', 'nulla'];
    //Regex - checking for valid Roman numerals
    protected $romanRegex = '/^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/';

    //Roman numeral validation function - is the string a valid Roman Number?
    public function validate($roman)
    {
        return preg_match($this->romanRegex, $roman) > 0;
    }

    //Conversion: Roman Numeral to Integer
    public function parse($roman)
    {
        //checking for zero values
        if (\in_array($roman, $this->romanZero, true)) {
            return 0;
        }

        $roman = strtoupper($roman);

        //validating string
        if (!$this->validate($roman)) {
            return false;
        }

        $values = $this->romanValues;
        $result = 0;
        //iterating through characters LTR
        for ($i = 0, $length = \strlen($roman); $i < $length; ++$i) {
            //getting value of current char
            $value = $values[$roman[$i]];
            //getting value of next char - null if there is no next char
            $nextvalue = !isset($roman[$i + 1]) ? null : $values[$roman[$i + 1]];
            //adding/subtracting value from result based on $nextvalue
            $result += (null !== $nextvalue && $nextvalue > $value) ? -$value : $value;
        }

        return $result;
    }

    public function convert($integer)
    {
        $table = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $return = '';
        while ($integer > 0) {
            foreach ($table as $rom => $arb) {
                if ($integer >= $arb) {
                    $integer -= $arb;
                    $return .= $rom;

                    break;
                }
            }
        }

        return $return;
    }
}
