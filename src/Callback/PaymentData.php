<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

class PaymentData extends CallbackData
{
    private $currency;
    private $convertedAmount;
    private $cardholder;

    public function __construct(
        string $id,
        int $amount,
        string $transactionId,
        \DateTime $date,
        string $result,
        ?int $currency = null,
        ?int $convertedAmount = null,
        ?CardholderData $cardholder = null
    ) {
        parent::__construct($id, $amount, $transactionId, $date, $result);

        $this->currency = $currency;
        $this->convertedAmount = $convertedAmount;
        $this->cardholder = $cardholder;
    }

    public function getCurrency(): ?int
    {
        return $this->currency;
    }

    public function getConvertedAmount(): ?int
    {
        return $this->convertedAmount;
    }

    public function getCardholderData(): ?CardholderData
    {
        return $this->cardholder;
    }
}
