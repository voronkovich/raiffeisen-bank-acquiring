<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\SecretKey;

class SecretKeyKeyTest extends TestCase
{
    /**
     * @testdox Can be instantiated from Base64 encoded string
     */
    public function testCanBeInstantiatedFromBase64EncodedString()
    {
        $secret = 'secret';

        $key = SecretKey::fromBase64(\base64_encode($secret));

        $this->assertSame($secret, $key->getValue());
    }

    /**
     * @testdox Throws exception if key is not Base64 encoded
     */
    public function testThrowsExceptionWhenKeyIsNotBase64Encoded()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided key is not base64-encoded.');

        $key = SecretKey::fromBase64('@@@');
    }

    /**
     * @testdox Can be instantiated from Hex encoded string
     */
    public function testCanBeInstantiatedFromHexEncodedString()
    {
        $secret = 'secret';

        $key = SecretKey::fromHex(\bin2hex($secret));

        $this->assertSame($secret, $key->getValue());
    }

    /**
     * @testdox Throws exception if key is not Hex encoded
     */
    public function testThrowsExceptionWhenKeyIsNotHexEncoded()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided key is not hex-encoded.');

        $key = SecretKey::fromHex('===');
    }

    /**
     * @testdox Can be converted from Base64 to Hex format
     */
    public function testCanBeConvertedFromBase64ToHexFormat()
    {
        $secret = 'secret';

        $key = SecretKey::fromBase64(\base64_encode($secret));

        $this->assertSame(\bin2hex($secret), $key->toHex());
    }

    /**
     * @testdox Can be converted from Hex to Base64 format
     */
    public function testCanBeConvertedFromHexToBase64Format()
    {
        $secret = 'secret';

        $key = SecretKey::fromBase64(\base64_encode($secret));

        $this->assertSame(\bin2hex($secret), $key->toHex());
    }
}
