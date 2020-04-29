<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\SecretKey;
use Voronkovich\RaiffeisenBankAcquiring\SignatureGenerator;

class SignatureGeneratorTest extends TestCase
{
    public function testGeneratesBaseSignature()
    {
        // Base64 encoded 'secret' string
        $secret = 'c2VjcmV0';
        $merchantId = '1680024001';
        $terminalId = '80024001';
        $paymentId = 'test_descriptor';
        $paymentAmount = '1.00';

        $key = SecretKey::fromBase64($secret);
        $signatureGenerator = SignatureGenerator::useBase64Encoding($key);

        $signature = $signatureGenerator->base($merchantId, $terminalId, $paymentId, $paymentAmount);

        $this->assertEquals('PydO7jX5BZVYWGa45ZRWX54gstq/pQyFfHmKuGJt53A=', $signature);
    }
}
