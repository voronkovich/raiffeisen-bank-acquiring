<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Payment;

/**
 * Represent languages code supported by RaiffeisenBank.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class Language
{
    public const RU = 'ru';
    public const EN = 'en';

    private const LANGUAGES = [
        self::RU => '01',
        self::EN => '02',
    ];

    public static function fromIsoCode(string $isoCode): string
    {
        return self::LANGUAGES[$isoCode];
    }
}
