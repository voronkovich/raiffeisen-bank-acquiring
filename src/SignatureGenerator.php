<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;

class SignatureGenerator
{
    private $key;

    public static function fromBase64(string $base64EncodedKey): self
    {
        $key = @\base64_decode($base64EncodedKey, true);

        if (false === $key) {
            throw new InvalidArgumentException('Provided key is not base64-encoded.');
        }

        return new self($key);
    }

    public static function fromHex(string $hexEncodedKey): self
    {
        $key = @\hex2bin($hexEncodedKey);

        if (false === $key) {
            throw new InvalidArgumentException('Provided key is not hex-encoded.');
        }

        return new self($key);
    }

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function base(string $merchantId, string $terminalId, $paymentId, string $paymentAmount): string
    {
        return $this->generate(\func_get_args());
    }

    public function generate(array $chunks): string
    {
        return \hash_hmac('sha256', \implode(';', $chunks), $this->key, true);
    }
}
