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
    const RECAPTCHA_VERIFY_SERVER = 'https://www.google.com';

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /** @var HgabkaUtils */
    protected $hgabkaUtils;

    /**
     * RecaptchaValidator constructor.
     *
     * @param string $secret
     */
    public function __construct(RequestStack $requestStack, HgabkaUtils $hgabkaUtils, $secret)
    {
        $this->secret = $secret;
        $this->requestStack = $requestStack;
        $this->hgabkaUtils = $hgabkaUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Recaptcha) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Recaptcha');
        }

        $request = $this->requestStack->getCurrentRequest();
        $remoteip = $request->getClientIp();
        $response = $request->get('g-recaptcha-response');

        $isValid = $this->checkAnswer($this->secret, $remoteip, $response);
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
    private function checkAnswer($privateKey, $remoteip, $response)
    {
        if (null === $remoteip || '' === $remoteip) {
            throw new ValidatorException('For security reasons, you must pass the remote ip to reCAPTCHA');
        }
        // discard spam submissions
        if (null === $response || 0 === \strlen($response)) {
            return false;
        }
        $result = $this->hgabkaUtils->curlPost(self::RECAPTCHA_VERIFY_SERVER.'/recaptcha/api/siteverify', [
            'secret' => $privateKey,
            'remoteip' => $remoteip,
            'response' => $response,
        ]);

        $result = json_decode($result, true);

        if (isset($result['success']) && true === $result['success']) {
            return true;
        }

        return false;
    }
}
