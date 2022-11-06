<?php

namespace Hgabka\UtilsBundle\Validator\Constraints;

use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class RecaptchaValidator extends ConstraintValidator
{
    /**
     * The reCAPTCHA server URL's.
     */
    public const RECAPTCHA_VERIFY_SERVER = 'https://www.google.com';

    public function __construct(protected readonly RequestStack $requestStack, protected readonly HgabkaUtils $hgabkaUtils, protected readonly ?string $secret) {}

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Recaptcha) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Recaptcha');
        }

        $request = $this->requestStack->getCurrentRequest();
        $remoteip = $request->getClientIp();
        $response = $request->get('g-recaptcha-response');

        $isValid = $this->checkAnswer($this->secret, $remoteip, $response, $constraint->mode, $constraint->minimumScore);
        if (!$isValid) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * Calls an HTTP POST function to verify if the user's guess was correct.
     *
     * @param string $privateKey
     * @param string $remoteip
     * @param string $response
     *
     * @throws ValidatorException When missing remote ip
     *
     * @return bool
     */
    private function checkAnswer(?string $privateKey, ?string $remoteip, ?string $response, ?string $mode, float $minimumScore): bool
    {
        if (null === $remoteip || '' === $remoteip) {
            throw new ValidatorException('For security reasons, you must pass the remote ip to reCAPTCHA');
        }
        // discard spam submissions
        if (null === $response || '' === $response) {
            return false;
        }
        $result = $this->hgabkaUtils->curlPost(self::RECAPTCHA_VERIFY_SERVER . '/recaptcha/api/siteverify', [
            'secret' => $privateKey,
            'remoteip' => $remoteip,
            'response' => $response,
        ]);

        $result = json_decode($result, true);

        if (isset($result['success']) && true === $result['success']) {
            if ('invisible' !== $mode) {
                return true;
            }

            return isset($result['score']) && (float) $result['score'] >= $minimumScore;
        }

        return false;
    }
}
