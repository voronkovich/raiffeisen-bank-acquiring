<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

class PaymentData extends CallbackData
{
    private $authorizationCode;
    private $errorMessage;
    private $currency;
    private $convertedAmount;
    private $cardholder;

    public function __construct(
        string $id,
        int $amount,
        string $transactionId,
        \DateTime $date,
        ?int $errorCode,
        ?string $errorMessage,
        ?string $authorizationCode,
        ?int $currency = null,
        ?int $convertedAmount = null,
        ?CardholderData $cardholder = null
    ) {
        parent::__construct($id, $amount, $transactionId, $date, $errorCode);

        $this->errorMessage = $errorMessage;
        $this->authorizationCode = $authorizationCode;
        $this->currency = $currency;
        $this->convertedAmount = $convertedAmount;
        $this->cardholder = $cardholder;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
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
