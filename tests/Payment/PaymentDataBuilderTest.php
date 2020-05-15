<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Payment;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\RequiredParameterMissingException;
use Voronkovich\RaiffeisenBankAcquiring\Payment\PaymentDataBuilder;
use Voronkovich\RaiffeisenBankAcquiring\Signature\Signature;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SignatureGenerator;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class PaymentDataBuilderTest extends TestCase
{
    private const RUB = 643;
    private const USD = 840;

    public function testAllowsToCreateSimplePaymentWithDefaultCurrency()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $expected = [
            'PurchaseDesc' => 123,
            'PurchaseAmt' => '50.34',
            'MerchantID' => '000001680024001-80024001',
            'MerchantName' => 'VeryCoolShop',
            'CountryCode' => 643,
            'CurrencyCode' => 643,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
            'HMAC' => 'H8uGBid+sbxCSkPs/LgaEyLoVuZRxBpIvbiNoiC3sZk=',
        ];

        $this->assertEquals($expected, $data);
    }

    public function testAllowsToSetPaymentCurrency()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setCurrency(self::USD)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $expected = [
            'PurchaseDesc' => 123,
            'PPurchaseAmt' => '50.34',
            'PurchaseAmt' => 0,
            'PCurrencyCode' => 840,
            'MerchantID' => '000001680024001-80024001',
            'MerchantName' => 'VeryCoolShop',
            'CountryCode' => 643,
            'CurrencyCode' => 643,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
            'Options' => 'C',
            'HMAC' => '/pFFECgAiNcqs3ZbtrfBhfD8AUy6kDtm2/W5w5DXgzI=',
        ];

        $this->assertEquals($expected, $data);
    }

    public function testAllowsToSetPaymentTimeLimit()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setCreationDate(new \DateTimeImmutable('2020-05-09'))
            ->setLifetime(3600)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $expected = [
            'PurchaseDesc' => 123,
            'PurchaseAmt' => '50.34',
            'Time' => 1588971600,
            'Window' => 3600,
            'MerchantID' => '000001680024001-80024001',
            'MerchantName' => 'VeryCoolShop',
            'CountryCode' => 643,
            'CurrencyCode' => 643,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
            'Options' => 'T',
            'HMAC' => 'LIN9LrWv4hMcy7qqqNF20ZyFs9yHdVnDM5T++phHaTM=',
        ];

        $this->assertEquals($expected, $data);
    }

    public function testUsesCurrentDateIfPaymentCreationDateNotSet()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $time = \time();

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setLifetime(3600)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $this->assertGreaterThanOrEqual($time, $data['Time']);
    }

    public function testAllowsToSetPaymentCurrencyAndTimeLimitAtTheSameTime()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setCurrency(self::USD)
            ->setCreationDate(new \DateTimeImmutable('2020-05-09'))
            ->setLifetime(3600)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $expected = [
            'PurchaseDesc' => 123,
            'PPurchaseAmt' => '50.34',
            'PurchaseAmt' => 0,
            'PCurrencyCode' => 840,
            'Time' => 1588971600,
            'Window' => 3600,
            'MerchantID' => '000001680024001-80024001',
            'MerchantName' => 'VeryCoolShop',
            'CountryCode' => 643,
            'CurrencyCode' => 643,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
            'Options' => 'CT',
            'HMAC' => 'Pc/wpJFSxNV/0MLhzTOiBSbTjvNk1l8b+bpDGXkHt9Q=',
        ];

        $this->assertEquals($expected, $data);
    }

    public function testAllowsToRequireCaldholderInformation()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->requireCardholderName()
            ->requireCardholderEmail()
            ->requireCardholderPhone()
            ->requireCardholderCountry()
            ->requireCardholderCity()
            ->requireCardholderAddress()
            ->getData()
        ;

        $this->assertEquals('Y', $data['CardholderName']);
        $this->assertEquals('Y', $data['Email']);
        $this->assertEquals('Y', $data['Phone']);
        $this->assertEquals('Y', $data['Country']);
        $this->assertEquals('Y', $data['City']);
        $this->assertEquals('Y', $data['Address']);
    }

    public function testAllowsToPassExternalData()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setLifetime(3600)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->setExt1('ext1')
            ->setExt2('ext2')
            ->getData()
        ;

        $this->assertEquals('ext1', $data['Ext1']);
        $this->assertEquals('ext2', $data['Ext2']);
    }

    public function testAllowsToSetInterfaceLanguage()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1689996001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('89996001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->setLanguage('en')
            ->getData()
        ;

        $this->assertEquals('02', $data['Language']);
    }

    public function testAllowsToUseMobileDesign()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->useMobileDesign()
            ->getData()
        ;

        $this->assertEquals('Y', $data['Mobile']);
    }

    public function testSupportsHexEncodedSignature()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::HEX))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1680024001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $expected = [
            'PurchaseDesc' => 123,
            'PurchaseAmt' => '50.34',
            'MerchantID' => '000001680024001-80024001',
            'MerchantName' => 'VeryCoolShop',
            'CountryCode' => 643,
            'CurrencyCode' => 643,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
            'Options' => 'H',
            'HMAC' => '1fcb8606277eb1bc424a43ecfcb81a1322e856e651c41a48bdb88da220b7b199',
        ];

        $this->assertEquals($expected, $data);
    }

    public function testThrowsAnExceptionIfRequiredParameterIsMissing()
    {
        $this->expectException(RequiredParameterMissingException::class);
        $this->expectExceptionMessage('Required parameter "amount" is missing.');

        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(100)
            ->setMerchantId('1689996001')
            ->setMerchantName('VeryCoolShop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('89996001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;
    }
}
