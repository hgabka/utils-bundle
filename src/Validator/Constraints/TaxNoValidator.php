<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Hgabka\UtilsBundle\Validator\TaxValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNoValidator extends ConstraintValidator
{
    /**
     * TaxNoValidator constructor.
     */
    public function __construct(private readonly TaxValidator $taxValidator)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
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
