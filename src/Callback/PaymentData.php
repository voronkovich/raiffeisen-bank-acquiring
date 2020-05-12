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
        string $result,
        ?string $authorizationCode,
        ?string $errorMessage,
        ?int $currency = null,
        ?int $convertedAmount = null,
        ?CardholderData $cardholder = null
    ) {
        parent::__construct($id, $amount, $transactionId, $date, $result);

        $this->authorizationCode = $authorizationCode;
        $this->errorMessage = $errorMessage;
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
