<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\PaymentData;

class PaymentDataTest extends TestCase
{
    private const RUB = 643;

    public function testCreatesDataForSimplePayment()
    {
        $data = (new PaymentData())
            ->setId(100)
            ->setAmount('50.34')
            ->setMerchantId('000001234567890-12345678')
            ->setMerchantName('Very Cool Shop')
            ->setMerchantCountry(self::RUB)
            ->setMerchantCurrency(self::RUB)
            ->setMerchantCity('MOSCOW')
            ->setMerchantUrl('https://verycoolshop.abc')
            ->setSuccessUrl('https://verycoolshop.abc/success')
            ->setFailUrl('https://verycoolshop.abc/fail')
            ->getData()
        ;

        $this->assertEquals($data, [
            'PurchaseDesc' => 100,
            'PurchaseAmt' => '50.34',
            'MerchantID' => '000001234567890-12345678',
            'MerchantName' => 'Very Cool Shop',
            'CountryCode' => self::RUB,
            'CurrencyCode' => self::RUB,
            'MerchantCity' => 'MOSCOW',
            'MerchantURL' => 'https://verycoolshop.abc',
            'SuccessURL' => 'https://verycoolshop.abc/success',
            'FailURL' => 'https://verycoolshop.abc/fail',
        ]);
    }
}
