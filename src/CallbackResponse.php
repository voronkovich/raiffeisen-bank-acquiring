<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

class CallbackResponse
{
    private const SUCCESS = 0;
    private const ALREADY_HANDLED = 1;
    private const TEMPORARILY_UNAVAILABLE = -1;
    private const ERROR = -2;

    private $code;

    private function __construct(int $code)
    {
        $this->code = $code;
    }

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function alreadyHandled(): self
    {
        return new self(self::ALREADY_HANDLED);
    }

    public static function temporaryUnavailable(): self
    {
        return new self(self::TEMPORARILY_UNAVAILABLE);
    }

    public static function error(): self
    {
        return new self(self::ERROR);
    }

    public function __toString(): string
    {
        return \sprintf('RESP_CODE(%d)', $this->code);
    }
}
