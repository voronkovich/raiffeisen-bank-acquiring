<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class MerchantIdFormatter
{
    private const FORMAT = '00000%d-%d';

    public static function format(string $merchantId, string $terminalId): string
    {
        return \sprintf(self::FORMAT, $merchantId, $terminalId);
    }

    public static function parse(string $value): array
    {
        $result = \sscanf($value, self::FORMAT);

        if (-1 === $result || \in_array(null, $result)) {
            throw new InvalidArgumentException(
                \sprintf('Value "%s" has invalid format. Expected format: "%s".', $value, self::FORMAT)
            );
        }

        return $result;
    }
}
