<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Signature;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SecretKey;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SignatureGenerator;

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

        $signatureGenerator = SignatureGenerator::base64($secret);

        $signature = $signatureGenerator->base($merchantId, $terminalId, $paymentId, $paymentAmount);

        $this->assertEquals('PydO7jX5BZVYWGa45ZRWX54gstq/pQyFfHmKuGJt53A=', $signature->base64());
        $this->assertEquals('3f274eee35f90595585866b8e594565f9e20b2dabfa50c857c798ab8626de770', $signature->hex());
    }
}
