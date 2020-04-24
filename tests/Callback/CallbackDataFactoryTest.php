<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Callback;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Callback\CallbackDataFactory;
use Voronkovich\RaiffeisenBankAcquiring\Callback\CallbackPaymentData;
use Voronkovich\RaiffeisenBankAcquiring\Callback\CallbackReversalData;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidCallbackDataException;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidCallbackException;

class CallbackDataFactoryTest extends TestCase
{
    public function testCreatesPaymentCallbackDataFromArray()
    {
        $callbackDataFactory = new CallbackDataFactory();

        $data = [
            'type' => 'conf_pay',
            'id' => '4873558',
            'descr' => '12343498',
            'amt' => '234,33',
            'date' => '2011-12-25 16:05:24',
            'result' => '0',
        ];

        $payment = $callbackDataFactory->fromArray($data);

        $this->assertInstanceOf(CallbackPaymentData::class, $payment);
        $this->assertEquals('12343498', $payment->getId());
        $this->assertEquals('234,33', $payment->getAmount());
        $this->assertEquals('4873558', $payment->getTransactionId());
        $this->assertEquals(new \DateTime('2011-12-25 16:05:24'), $payment->getDate());
        $this->assertTrue($payment->isSuccessfull());
    }

    public function testCreatesReversalCallbackDataFromArray()
    {
        $callbackDataFactory = new CallbackDataFactory();

        $data = [
            'type' => 'conf_reversal',
            'id' => '4873558',
            'descr' => '123456789',
            'amt' => '100,10',
            'date' => '2011-12-25 16:05:24',
            'result' => '0',
        ];

        $reversal = $callbackDataFactory->fromArray($data);

        $this->assertInstanceOf(CallbackReversalData::class, $reversal);
        $this->assertEquals('123456789', $reversal->getId());
        $this->assertEquals('100,10', $reversal->getAmount());
        $this->assertEquals('4873558', $reversal->getTransactionId());
        $this->assertEquals(new \DateTime('2011-12-25 16:05:24'), $reversal->getDate());
        $this->assertTrue($reversal->isSuccessfull());
    }

    public function testThrowsExceptionIfCallbackTypeIsNotDefined()
    {
        $this->expectException(InvalidCallbackDataException::class);
        $this->expectExceptionMessage('Callback parameter "type" is not defined.');

        $callbackDataFactory = new CallbackDataFactory();

        $callbackDataFactory->fromArray([]);
    }
}
