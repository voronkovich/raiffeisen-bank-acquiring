<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

class SignatureGenerator
{
    private $key;

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
