<?php

namespace Hgabka\UtilsBundle\Tests\Helper;

use Hgabka\UtilsBundle\Barcode\BarcodeValidator;
use Hgabka\UtilsBundle\Util\PasswordUpdater;
use Hgabka\UtilsBundle\Util\TokenGenerator;
use Hgabka\UtilsBundle\Validator\TaxValidator;
use Hgabka\UtilsBundle\Model\AbstractUser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class ValidatorsTest extends TestCase
{
    #[DataProvider('taxNumbers')]
    public function testTaxNo(string $value, bool $valid): void
    {
        $this->assertSame($valid, (new TaxValidator())->validateTaxNo($value));
    }

    public static function taxNumbers(): array
    {
        return [
            'valid' => ['8123456786', true],
            'wrong check digit' => ['8123456785', false],
            'must start with 8' => ['7123456786', false],
            'too short' => ['812345678', false],
            'letters' => ['81234567AB', false],
        ];
    }

    #[DataProvider('taxIds')]
    public function testTaxId(string $value, bool $valid): void
    {
        $this->assertSame($valid, (new TaxValidator())->validateTaxId($value));
    }

    public static function taxIds(): array
    {
        return [
            'valid' => ['12345676-2-42', true],
            'wrong check digit' => ['12345675-2-42', false],
            'bad vat code' => ['12345676-6-42', false],
            'bad county code' => ['12345676-2-99', false],
            'no dashes' => ['12345676242', false],
        ];
    }

    #[DataProvider('barcodes')]
    public function testBarcodeValidity(string $code, bool $valid): void
    {
        $this->assertSame($valid, (new BarcodeValidator($code))->isValid());
    }

    public static function barcodes(): array
    {
        return [
            'EAN-13' => ['4006381333931', true],
            'EAN-13 with spaces around' => [' 4006381333931 ', true],
            'EAN-13 wrong check digit' => ['4006381333932', false],
            'UPC-A' => ['036000291452', true],
            'EAN-8' => ['96385074', true],
            'GTIN-14' => ['40063813339318', false],
            'letters' => ['40063813339A1', false],
            'too short' => ['12345', false],
            'empty' => ['', false],
        ];
    }

    public function testBarcodeNormalisation(): void
    {
        $barcode = new BarcodeValidator('4006381333931');

        $this->assertSame('4006381333931', $barcode->getBarcode());
        $this->assertSame('04006381333931', $barcode->getGTIN14());
        $this->assertSame(BarcodeValidator::TYPE_EAN, $barcode->getType());
    }

    public function testTokensAreUrlSafeAndUnique(): void
    {
        $generator = new TokenGenerator();
        $token = $generator->generateToken();

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{43}$/', $token);
        $this->assertNotSame($token, $generator->generateToken());
    }

    public function testPasswordIsHashedAndCredentialsErased(): void
    {
        $user = $this->createMock(AbstractUser::class);
        $user->method('getPlainPassword')->willReturn('secret');
        $user->expects($this->once())->method('setPassword')->with('hashed:secret');
        $user->expects($this->once())->method('eraseCredentials');
        $hasher = $this->createStub(PasswordHasherInterface::class);
        $hasher->method('hash')->willReturnCallback(static fn (string $plain): string => 'hashed:' . $plain);
        $factory = $this->createStub(PasswordHasherFactoryInterface::class);
        $factory->method('getPasswordHasher')->willReturn($hasher);

        (new PasswordUpdater($factory))->hashPassword($user);
    }

    #[DataProvider('emptyPasswords')]
    public function testEmptyPlainPasswordsAreIgnored(?string $plain): void
    {
        $user = $this->createMock(AbstractUser::class);
        $user->method('getPlainPassword')->willReturn($plain);
        $user->expects($this->never())->method('setPassword');
        $user->expects($this->never())->method('eraseCredentials');

        (new PasswordUpdater($this->createStub(PasswordHasherFactoryInterface::class)))->hashPassword($user);
    }

    public static function emptyPasswords(): array
    {
        return [[null], ['']];
    }
}
