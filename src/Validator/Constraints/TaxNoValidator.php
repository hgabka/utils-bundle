<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Hgabka\UtilsBundle\Validator\TaxValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNoValidator extends ConstraintValidator
{
    /** @var TaxValidator */
    protected $taxValidator;

    /**
     * TaxNoValidator constructor.
     */
    public function __construct(TaxValidator $taxValidator)
    {
        $this->taxValidator = $taxValidator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->taxValidator->validateTaxNo($value)) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}
