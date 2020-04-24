<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Payment\PaymentForm;

class PaymentFormTest extends TestCase
{
    public function testRendersHtmlFormForPaymentData()
    {
        $data = [
            'PurchaseAmt' => '30.00',
            'PurchaseDesc' => '11',
        ];

        $form = new PaymentForm($data, 'Pay');

        $this->assertEquals(
            '<form action="https://e-commerce.raiffeisen.ru/vsmc3ds/pay_check/3dsproxy_init.jsp" method="POST"><input type="hidden" name="PurchaseAmt" value="30.00" /><input type="hidden" name="PurchaseDesc" value="11" /><input type="submit" value="Pay" /></form>',
            (string) $form
        );
    }
}
