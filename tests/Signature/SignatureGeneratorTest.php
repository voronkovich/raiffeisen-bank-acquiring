<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Signature;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SignatureGenerator;

class SignatureGeneratorTest extends TestCase
{
    public function testGeneratesBaseSignature()
    {
        // Base64 encoded 'secret' string
        $signatureGenerator = SignatureGenerator::base64('c2VjcmV0');

        $merchantId = '1680024001';
        $terminalId = '80024001';
        $paymentId = 'test_descriptor';
        $paymentAmount = '1.00';

        $signature = $signatureGenerator->generate([ $merchantId, $terminalId, $paymentId, $paymentAmount ]);

        $this->assertEquals('PydO7jX5BZVYWGa45ZRWX54gstq/pQyFfHmKuGJt53A=', $signature->base64());
        $this->assertEquals('3f274eee35f90595585866b8e594565f9e20b2dabfa50c857c798ab8626de770', $signature->hex());
    }
}
