<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class TaxNo extends Constraint
{
    public string $message = 'hg_utils.tax_no.message';
}
