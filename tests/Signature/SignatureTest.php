<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Signature;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\Signature\Signature;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class SignatureTest extends TestCase
{
    /**
     * @testdox Encodes signature to base64
     */
    public function testEncodesSignatureToBase64()
    {
        $signature = new Signature('xxx');

        $this->assertEquals('eHh4', $signature->base64());
        $this->assertEquals('eHh4', $signature->getValue(Signature::BASE64));
    }

    public function testEncodesSignatureToHex()
    {
        $signature = new Signature('xxx');

        $this->assertEquals('787878', $signature->hex());
        $this->assertEquals('787878', $signature->getValue(Signature::HEX));
    }

    public function testThrowsExceptionIfEncodingIsInvalid()
    {
        $signature = new Signature('xxx');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid encoding: "utf-8".');

        $signature->getValue('utf-8');
    }
}
