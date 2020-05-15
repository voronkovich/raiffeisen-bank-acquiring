<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;

/**
 * Converts amount from minor representation to formatted and back.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class AmountConverter
{
    /**
     * Minor and major amount parts delimiter (e.g. '.').
     *
     * @var string
     */
    private $delimiter;

    /**
     * @param $delimiter Delimiter (e.g. '.')
     */
    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Create amount converter for creating payment.
     */
    public static function forPayment(): self
    {
        static $converter = null;

        if (null === $converter) {
            return new self('.');
        }

        return $converter;
    }

    /**
     * Create amount converter for parsing callback data.
     */
    public static function forCallback(): self
    {
        static $converter = null;

        if (null === $converter) {
            return new self(',');
        }

        return $converter;
    }

    /**
     * Convert minor representation to formatted.
     *
     * @param $amount Amount in minor representation (e.g. 1000)
     *
     * @return string Formatted representation (e.g. '10.00')
     */
    public function minorToFormatted(int $amount): string
    {
        return \sprintf("%d%s%'02d", \intdiv($amount, 100), $this->delimiter, $amount % 100);
    }

    /**
     * Convert formatted representation to minor.
     *
     * @param string $amount Formatted amount (e.g. '10.00')
     *
     * @throws InvalidArgumentException If amount has invalid format
     *
     * @return int Minor representation (e.g. 1000)
     */
    public function formattedToMinor(string $amount): int
    {
        $regex = \sprintf('/^(?<major>\d+)(%s(?<minor>\d\d?))?$/', \preg_quote($this->delimiter));

        if (!\preg_match($regex, $amount, $chunks)) {
            throw new InvalidArgumentException(\sprintf('Amount has invalid format: %s.', $amount));
        }

        $major = $chunks['major'];
        $minor = $chunks['minor'] ?? '00';

        return (int) ($major.$minor.(\strlen($minor) === 1 ? '0' : ''));
    }
}
