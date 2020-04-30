<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Signature;

use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;

class Signature
{
    public const BASE64 = 'base64';
    public const HEX = 'hex';

    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(string $encoding = null): string
    {
        switch ($encoding) {
            case self::BASE64:
                return $this->base64();
            case self::HEX:
                return $this->hex();
            case null:
                return $this->value;
        }

        throw new InvalidArgumentException(\sprintf('Invalid encoding: "%s".', $encoding));
    }

    public function base64(): string
    {
        return \base64_encode($this->value);
    }

    public function hex(): string
    {
        return \bin2hex($this->value);
    }
}
