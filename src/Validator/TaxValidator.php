<?php

namespace Hgabka\UtilsBundle\Validator;

class TaxValidator
{
    public function validateTaxNo(string $value): bool
    {
        $pattern = '/^(\d{10})$/';

        if (!preg_match($pattern, $value, $matches)) {
            return false;
        }

        $digits = str_split($value);
        if (8 !== (int) $digits[0]) {
            return false;
        }

        $sum = 0;

        for ($i = 0; $i < 9; ++$i) {
            $sum += ($digits[$i] * ($i + 1));
        }

        if ($sum % 11 !== (int) $digits[9]) {
            return false;
        }

        return true;
    }

    public function validateTaxId(string $value): bool
    {
        $pattern = '/^(\d{7})(\d)\-([1-5])\-(0[2-9]|[13][0-9]|2[02-9]|4[0-4]|51)$/';

        if (!preg_match($pattern, $value, $matches)) {
            return false;
        }

        $muls = [9, 7, 3, 1, 9, 7, 3];
        $parts = explode('-', $value);
        $firstPartDigits = str_split($parts[0]);
        $check = $firstPartDigits[7];
        $sum = 0;

        for ($i = 0; $i < 7; ++$i) {
            $sum += ($firstPartDigits[$i] * $muls[$i]);
        }
        $lastDigit = $sum % 10;

        if ($lastDigit > 0) {
            $lastDigit = 10 - $lastDigit;
        }

        if ((int) $lastDigit !== (int) $check) {
            return false;
        }

        return true;
    }
}
