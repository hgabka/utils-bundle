<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Recaptcha extends Constraint
{
    public string $message = 'hg_utils.recaptcha.message';

    public string $mode = 'normal';

    public float $minimumScore = 0.2;

    public string $action = 'utils';

    #[HasNamedArguments]
    public function __construct(string $mode = 'normal', float $minimumScore = 0.2, ?string $action = 'utils', ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->mode = $mode;
        $this->minimumScore = $minimumScore;
        $this->action = $action;
    }
}
