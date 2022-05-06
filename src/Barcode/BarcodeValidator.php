<?php

namespace Hgabka\UtilsBundle\Barcode;

class BarcodeValidator
{
    public const TYPE_GTIN = 'GTIN';
    public const TYPE_EAN_8 = 'EAN-8';
    public const TYPE_EAN = 'EAN';
    public const TYPE_UPC = 'UPC';
    public const TYPE_UPC_COUPON_CODE = 'UPC Coupon Code';
    public const TYPE_EAN_RESTRICTED = 'EAN Restricted';
    private $barcode;
    private $type;
    private $gtin14;
    private $valid;

    public function __construct($barcode)
    {
        $this->barcode = $barcode;

        // Trims parsed string to remove unwanted whitespace or characters
        $this->barcode = trim($this->barcode);
        if (preg_match('/[^0-9]/', $this->barcode)) {
            $this->valid = false;

            return;
        }

        if (!is_string($this->barcode)) {
            $this->barcode = (string) ($this->barcode);
        }
        $this->gtin14 = $this->barcode;

        $length = strlen($this->gtin14);
        $this->valid = false;

        if (($length > 11 && $length <= 14) || 8 === $length) {
            $zeros = 18 - $length;
            $length = null;
            $fill = '';
            for ($i = 0; $i < $zeros; ++$i) {
                $fill .= '0';
            }

            $this->gtin14 = $fill . $this->gtin14;
            $fill = null;

            $this->valid = true;

            if (!$this->checkDigitValid()) {
                $this->valid = false;
            } elseif (substr($this->gtin14, 5, 1) > 2) {
                // EAN / JAN / EAN-13 code
                $this->type = self::TYPE_EAN;
            } elseif (0 === substr($this->gtin14, 6, 1) && 0 === substr($this->gtin14, 0, 10)) {
                // EAN-8 / GTIN-8 code
                $this->type = self::TYPE_EAN_8;
            } elseif (substr($this->gtin14, 5, 1) <= 0) {
                // UPC / UCC-12 GTIN-12 code
                if (5 === substr($this->gtin14, 6, 1)) {
                    $this->type = self::TYPE_UPC_COUPON_CODE;
                } else {
                    if (13 === strlen($this->barcode)) {
                        $this->type = self::TYPE_EAN;
                    } else {
                        $this->type = self::TYPE_UPC;
                    }
                }
            } elseif (0 === substr($this->gtin14, 0, 6)) {
                // GTIN-14 code
                $this->type = self::TYPE_GTIN;
            } else {
                // EAN code
                if (2 === substr($this->gtin14, 5, 1)) {
                    $this->type = self::TYPE_EAN_RESTRICTED;
                } else {
                    $this->type = self::TYPE_EAN;
                }
            }
        }
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getGTIN14(): string
    {
        return (string) substr($this->gtin14, -14);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    private function checkDigitValid(): bool
    {
        $calculation = 0;
        for ($i = 0; $i < (strlen($this->gtin14) - 1); ++$i) {
            $calculation += $i % 2 ? $this->gtin14[$i] * 1 : $this->gtin14[$i] * 3;
        }

        if (substr(10 - (substr($calculation, -1)), -1) !== substr($this->gtin14, -1)) {
            return false;
        }

        return true;
    }
}
