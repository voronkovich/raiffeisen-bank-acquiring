<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

class CallbackPaymentData extends CallbackData
{
    private $cardholder;

    public function __construct(
        string $id,
        int $amount,
        string $transactionId,
        \DateTime $date,
        string $result,
        CardholderData $cardholder = null
    ) {
        parent::__construct($id, $amount, $transactionId, $date, $result);

        $this->cardholder = $cardholder;
    }

    public function getCardholderData(): ?CardholderData
    {
        return $this->cardholder;
    }
}
