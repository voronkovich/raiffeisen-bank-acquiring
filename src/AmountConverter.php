<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;

class AmountConverter
{
    private $delimiter;

    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public static function forPayment(): self
    {
        static $converter = null;

        if (null === $converter) {
            return new self('.');
        }

        return $converter;
    }

    public static function forCallback(): self
    {
        static $converter = null;

        if (null === $converter) {
            return new self(',');
        }

        return $converter;
    }

    public function minorToFormatted(int $amount): string
    {
        return \sprintf("%d%s%'02d", \intdiv($amount, 100), $this->delimiter, $amount % 100);
    }

    public function formattedToMinor(string $amount): int
    {
        if (!\preg_match(\sprintf('/^(\d+)%s(\d\d)$/', $this->delimiter), $amount, $chunks)) {
            throw new InvalidArgumentException(\sprintf('Amount has invalid format: %s.', $amount));
        }

        return (int) ($chunks[1].$chunks[2]);
    }
}
