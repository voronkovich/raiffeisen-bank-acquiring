<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class ReversalData extends CallbackData
{
    public const TRANSACTION_ALREDY_REVERSED = 4;
    public const TRANSACTION_NOT_FOUND = 5;
    public const TRANSACTION_TOO_OLD = 8;

    public static function errorCodeToMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case self::TRANSACTION_ALREDY_REVERSED:
                return 'Transaction already reversed';
            case self::TRANSACTION_NOT_FOUND:
                return 'Transaction not found';
            case self::TRANSACTION_TOO_OLD:
                return 'Transaction too old';
        }

        return 'Unknown error';
    }
}
