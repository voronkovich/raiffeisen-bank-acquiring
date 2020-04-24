<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Payment;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\RequiredParameterMissingException;
use Voronkovich\RaiffeisenBankAcquiring\Payment\PaymentData;
use Voronkovich\RaiffeisenBankAcquiring\SignatureGenerator;

class PaymentDataTest extends TestCase
{
    private const RUB = 643;

    public function testCreatesDataForSimplePayment()
    {
        $data = (new PaymentData())
            ->setId(100)
            ->setAmount('50.34')
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

        $data = (new PaymentData())
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
        $data = (new PaymentData())
            ->setId(123)
            ->setAmount('50.34')
            ->setMerchantId('1689996001')
            ->setMerchantName('Very Cool Shop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setTerminalId('89996001')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->setSignatureGenerator(new SignatureGenerator('secret'))
            ->getData()
        ;

        $this->assertSame('2eA1i9nuRCnn09VI4WRPFFtWs9kH2RHI8WZZOgnkYxg=', $data['HMAC']);
    }
}
