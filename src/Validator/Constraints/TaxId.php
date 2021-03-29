<?php

namespace Hgabka\UtilsBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class TaxId extends Constraint
{
    public $message = 'hg_utils.validator.tax_id_format_error';
}
