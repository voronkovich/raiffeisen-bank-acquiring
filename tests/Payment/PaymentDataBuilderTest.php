<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Payment;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\RequiredParameterMissingException;
use Voronkovich\RaiffeisenBankAcquiring\Payment\PaymentDataBuilder;
use Voronkovich\RaiffeisenBankAcquiring\Signature\Signature;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SignatureGenerator;

class PaymentDataBuilderTest extends TestCase
{
    private const RUB = 643;

    public function testCreatesDataForSimplePayment()
    {
        $data = (new PaymentDataBuilder())
            ->setId(100)
            ->setAmount(5034)
            ->setMerchantId('1689996001')
            ->setMerchantName('Very Cool Shop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('89996001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $this->assertEquals($data, [
            'PurchaseDesc' => 100,
            'PurchaseAmt' => '50.34',
            'MerchantID' => '000001689996001-89996001',
            'MerchantName' => 'Very Cool Shop',
            'CountryCode' => self::RUB,
            'CurrencyCode' => self::RUB,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
        ]);
    }

    public function testThrowsAnExceptionIfRequiredParameterIsMissing()
    {
        $this->expectException(RequiredParameterMissingException::class);
        $this->expectExceptionMessage('Required parameter "amount" is missing.');

        $data = (new PaymentDataBuilder())
            ->setId(100)
            ->setMerchantId('1689996001')
            ->setMerchantName('Very Cool Shop')
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

    public function testGeneratesSignatureIfGeneratorProvided()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1689996001')
            ->setMerchantName('Very Cool Shop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('89996001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $this->assertSame('2eA1i9nuRCnn09VI4WRPFFtWs9kH2RHI8WZZOgnkYxg=', $data['HMAC']);
    }

    public function testSupportsHexEncodedSignature()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::HEX))
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1689996001')
            ->setMerchantName('Very Cool Shop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('89996001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $this->assertStringContainsString('H', $data['Options']);
        $this->assertSame('d9e0358bd9ee4429e7d3d548e1644f145b56b3d907d911c8f166593a09e46318', $data['HMAC']);
    }

    public function testSetsInterfaceLanguage()
    {
        $data = (new PaymentDataBuilder())
            ->setId(123)
            ->setAmount(5034)
            ->setMerchantId('1689996001')
            ->setMerchantName('Very Cool Shop')
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

    public function testSetsPaymentLifetime()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $time = \time();

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setLifetime(3600)
            ->setMerchantId('1680024001')
            ->setMerchantName('Very Cool Shop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('80024001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $this->assertEquals('3600', $data['Window']);
        $this->assertGreaterThanOrEqual($time, $data['Time']);
        $this->assertStringContainsString('T', $data['Options']);
    }

    public function testAllowsToRequireCaldholderInformation()
    {
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $data = (new PaymentDataBuilder($signatureGenerator, Signature::BASE64))
            ->setId(123)
            ->setAmount(5034)
            ->setLifetime(3600)
            ->setMerchantId('1680024001')
            ->setMerchantName('Very Cool Shop')
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
}
