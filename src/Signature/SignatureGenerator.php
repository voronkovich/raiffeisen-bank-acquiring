<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Signature;

use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;

class SignatureGenerator
{
    public const BASE64 = 'base64';
    public const HEX = 'hex';

    private $key;
    private $encoding;

    public static function useBase64Encoding(SecretKey $key): self
    {
        return new self($key, self::BASE64);
    }

    public static function useHexEncoding(SecretKey $key): self
    {
        return new self($key, self::HEX);
    }

    public function __construct(SecretKey $key, string $encoding)
    {
        if (!\in_array($encoding, [ self::BASE64, self::HEX ])) {
            throw new InvalidArgumentException(
                'Invalid encoding: %s. Supported values: %s,  %s.',
                $encoding,
                self::BASE64,
                self::HEX
            );
        }

        $this->key = $key;
        $this->encoding = $encoding;
    }

    public function getKey(): SecretKey
    {
        return $this->key;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function base(string $merchantId, string $terminalId, $paymentId, string $paymentAmount): string
    {
        return $this->generate(\func_get_args());
    }

    public function generate(array $chunks): string
    {
        $signature = \hash_hmac('sha256', \implode(';', $chunks), $this->key->getValue(), true);

        if ($this->isHexEncodingUsed()) {
            return \bin2hex($signature);
        }

        return \base64_encode($signature);
    }

    public function isBase64EncodingUsed(): bool
    {
        return self::BASE64 === $this->encoding;
    }

    public function isHexEncodingUsed(): bool
    {
        return self::HEX === $this->encoding;
    }
}
