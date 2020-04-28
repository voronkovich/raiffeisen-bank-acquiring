<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\SignatureGenerator;

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

    public function testCreatesSignaturGeneratorFromBase64EncodedKey()
    {
        $secret = \base64_encode('secret');
        $merchantId = '1689996001';
        $terminalId = '89996001';
        $paymentId = 123;
        $paymentAmount = '10.05';

        $signatureGenerator = SignatureGenerator::fromBase64($secret);

        $signature = $signatureGenerator->base($merchantId, $terminalId, $paymentId, $paymentAmount);

        $this->assertEquals('TcoWRL65V+gf+YVZv70DAqDcG+he2y26UjC5H3frDP0=', \base64_encode($signature));
    }

    public function testThrowsExceptionWhenKeyIsNotBase64Encoded()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided key is not base64-encoded.');

        $signatureGenerator = SignatureGenerator::fromBase64('@@@');
    }

    public function testCreatesSignaturGeneratorFromHexEncodedKey()
    {
        $secret = \bin2hex('secret');
        $merchantId = '1689996001';
        $terminalId = '89996001';
        $paymentId = 123;
        $paymentAmount = '10.05';

        $signatureGenerator = SignatureGenerator::fromHex($secret);

        $signature = $signatureGenerator->base($merchantId, $terminalId, $paymentId, $paymentAmount);

        $this->assertEquals('TcoWRL65V+gf+YVZv70DAqDcG+he2y26UjC5H3frDP0=', \base64_encode($signature));
    }

    public function testThrowsExceptionWhenKeyIsNotHexEncoded()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided key is not hex-encoded.');

        $signatureGenerator = SignatureGenerator::fromHex('===');
    }
}
