<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Recaptcha extends Constraint
{
    public $message = 'hg_utils.recaptcha.message';
}
