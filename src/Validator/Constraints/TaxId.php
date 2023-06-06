<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class TaxId extends Constraint
{
    public string $message = 'hg_utils.tax_id.message';
}
