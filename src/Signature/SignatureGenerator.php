<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Signature;

class SignatureGenerator
{
    private $key;

    public function __construct(SecretKey $key)
    {
        $this->key = $key;
    }

    public static function base64(string $key): self
    {
        return new self(SecretKey::base64($key));
    }

    public static function hex(string $key): self
    {
        return new self(SecretKey::hex($key));
    }

    public function base(string $merchantId, string $terminalId, $paymentId, string $paymentAmount): Signature
    {
        return $this->generate(\func_get_args());
    }

    public function callback(string $id, string $amount, string $result): Signature
    {
        return $this->generate(\func_get_args());
    }

    public function generate(array $chunks): Signature
    {
        $signature = \hash_hmac('sha256', \implode(';', $chunks), $this->key->getValue(), true);

        return new Signature($signature);
    }
}
