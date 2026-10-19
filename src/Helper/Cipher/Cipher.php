<?php

namespace Hgabka\UtilsBundle\Helper\Cipher;

use InvalidArgumentException;

/**
 * Cipher, this class can be used to encrypt and decrypt string values.
 */
class Cipher implements CipherInterface
{
    /**
     * The old mcrypt (Rijndael-256) based implementation does not exist any more on PHP 7.2+,
     * values encrypted with it cannot be decrypted by this class.
     */
    private const METHOD = 'aes-256-cbc';

    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        if (empty($secret)) {
            throw new InvalidArgumentException('You need to configure a Cipher secret in your parameters.yaml before you can use this!');
        }
        $this->secret = $secret;
    }

    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt($value)
    {
        $encrypted = openssl_encrypt((string) $value, self::METHOD, $this->getKey(), \OPENSSL_RAW_DATA, $this->getIv());

        return base64_encode($encrypted);
    }

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        $decoded = base64_decode((string) $value, true);
        if (false === $decoded) {
            return false;
        }

        return openssl_decrypt($decoded, self::METHOD, $this->getKey(), \OPENSSL_RAW_DATA, $this->getIv());
    }

    private function getKey(): string
    {
        return md5($this->secret);
    }

    private function getIv(): string
    {
        return substr(md5(md5($this->secret)), 0, 16);
    }
}
