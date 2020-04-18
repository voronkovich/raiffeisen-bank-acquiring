<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use Voronkovich\RaiffeisenBankAcquiring\SignatureGenerator;
use PHPUnit\Framework\TestCase;

class SignatureGeneratorTest extends TestCase
{
    public function testGeneratesBaseSignature()
    {
        $secret = 'secret';
        $merchantId = '1689996001';
        $terminalId = '89996001';
        $paymentId = 123;
        $paymentAmount = '10.05';

        $signatureGenerator = new SignatureGenerator($secret);
        $signature = $signatureGenerator->base($merchantId, $terminalId, $paymentId, $paymentAmount);

        $this->assertEquals('TcoWRL65V+gf+YVZv70DAqDcG+he2y26UjC5H3frDP0=', \base64_encode($signature));
    }
}
