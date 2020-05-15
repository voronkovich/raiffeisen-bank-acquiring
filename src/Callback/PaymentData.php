<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class PaymentData extends CallbackData
{
    private $authorizationCode;
    private $currency;
    private $convertedAmount;
    private $cardholder;
    private $ext1;
    private $ext2;

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
        ?CardholderData $cardholder = null,
        ?string $ext1 = null,
        ?string $ext2 = null
    ) {
        parent::__construct($id, $amount, $transactionId, $date, $errorCode, $errorMessage);

        $this->authorizationCode = $authorizationCode;
        $this->currency = $currency;
        $this->convertedAmount = $convertedAmount;
        $this->cardholder = $cardholder;
        $this->ext1 = $ext1;
        $this->ext2 = $ext2;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
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

    public function getExt1(): ?string
    {
        return $this->ext1;
    }

    public function getExt2(): ?string
    {
        return $this->ext2;
    }
}
