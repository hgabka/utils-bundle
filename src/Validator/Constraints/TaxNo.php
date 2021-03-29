<?php

namespace Hgabka\UtilsBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class TaxNo extends Constraint
{
    public $message = 'hg_utils.validator.tax_no_format_error';
}
