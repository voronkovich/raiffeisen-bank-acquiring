<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests\Signature;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SecretKey;

class SecretKeyTest extends TestCase
{
    /**
     * @testdox Can be instantiated from Base64 encoded string
     */
    public function testCanBeInstantiatedFromBase64EncodedString()
    {
        $key = SecretKey::base64('c2VjcmV0');

        $this->assertSame('secret', $key->getValue());
    }

    /**
     * @testdox Throws exception if key is not Base64 encoded
     */
    public function testThrowsExceptionWhenKeyIsNotBase64Encoded()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided key is not base64-encoded.');

        $key = SecretKey::base64('@@@');
    }

    /**
     * @testdox Can be instantiated from Hex encoded string
     */
    public function testCanBeInstantiatedFromHexEncodedString()
    {
        $key = SecretKey::hex('736563726574');

        $this->assertSame('secret', $key->getValue());
    }

    /**
     * @testdox Throws exception if key is not Hex encoded
     */
    public function testThrowsExceptionWhenKeyIsNotHexEncoded()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided key is not hex-encoded.');

        $key = SecretKey::hex('===');
    }

    /**
     * @testdox Can be converted from Base64 to Hex format
     */
    public function testCanBeConvertedFromBase64ToHexFormat()
    {
        $key = SecretKey::base64('c2VjcmV0');

        $this->assertSame('736563726574', $key->toHex());
    }

    /**
     * @testdox Can be converted from Hex to Base64 format
     */
    public function testCanBeConvertedFromHexToBase64Format()
    {
        $key = SecretKey::hex('736563726574');

        $this->assertSame('c2VjcmV0', $key->toBase64());
    }
}
