<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring;

use Voronkovich\RaiffeisenBankAcquiring\Exception\RequiredParameterMissingException;

class PaymentData
{
    private $id;
    private $amount;
    private $merchantId;
    private $merchantName;
    private $merchantCountry;
    private $merchantCurrency;
    private $merchantCity;
    private $merchantUrl;
    private $successUrl;
    private $failUrl;

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    public function setMerchantName(string $merchantName): self
    {
        $this->merchantName = $merchantName;

        return $this;
    }

    public function setMerchantCountry(int $merchantCountry): self
    {
        $this->merchantCountry = $merchantCountry;

        return $this;
    }

    public function setMerchantCurrency(int $merchantCurrency): self
    {
        $this->merchantCurrency = $merchantCurrency;

        return $this;
    }

    public function setMerchantCity(string $merchantCity): self
    {
        $this->merchantCity = $merchantCity;

        return $this;
    }

    public function setMerchantUrl(string $merchantUrl): self
    {
        $this->merchantUrl = $merchantUrl;

        return $this;
    }

    public function setSuccessUrl(string $successUrl): self
    {
        $this->successUrl = $successUrl;

        return $this;
    }

    public function setFailUrl(string $failUrl): self
    {
        $this->failUrl = $failUrl;

        return $this;
    }

    public function getData(): array
    {
        $this->checkRequiredParameters();

        return [
            'PurchaseDesc' => $this->id,
            'PurchaseAmt' => $this->amount,
            'MerchantID' => $this->merchantId,
            'MerchantName' => $this->merchantName,
            'CountryCode' => $this->merchantCountry,
            'CurrencyCode' => $this->merchantCurrency,
            'MerchantCity' => $this->merchantCity,
            'MerchantURL' => $this->merchantUrl,
            'SuccessURL' => $this->successUrl,
            'FailURL' => $this->failUrl,
        ];
    }

    private function checkRequiredParameters(): void
    {
        $requiredParameters = [
            'id',
            'amount',
            'merchantId',
            'merchantName',
            'merchantCountry',
            'merchantCurrency',
            'merchantCity',
            'merchantUrl',
            'successUrl',
            'failUrl',
        ];

        foreach ($requiredParameters as $parameter) {
            if (null === $this->$parameter || '' === $this->$parameter) {
                throw new RequiredParameterMissingException($parameter);
            }
        }
    }
}
