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
    private $terminalId;
    private $successUrl;
    private $failUrl;
    private $signatureGenerator;

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

    public function setTerminalId(string $terminalId): self
    {
        $this->terminalId = $terminalId;

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

    public function setSignatureGenerator(SignatureGenerator $signatureGenerator): self
    {
        $this->signatureGenerator = $signatureGenerator;

        return $this;
    }

    public function getData(): array
    {
        $this->checkRequiredParameters();

        $data = [
            'PurchaseDesc' => $this->id,
            'PurchaseAmt' => $this->amount,
            'MerchantID' => \sprintf('00000%s-%s', $this->merchantId, $this->terminalId),
            'MerchantName' => $this->merchantName,
            'CountryCode' => $this->merchantCountry,
            'CurrencyCode' => $this->merchantCurrency,
            'MerchantCity' => $this->merchantCity,
            'MerchantURL' => $this->merchantUrl,
            'SuccessURL' => $this->successUrl,
            'FailURL' => $this->failUrl,
        ];

        if (null !== $this->signatureGenerator) {
            $data['HMAC'] = $this->generateSignature();
        }

        return $data;
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
            'terminalId',
            'successUrl',
            'failUrl',
        ];

        foreach ($requiredParameters as $parameter) {
            if (null === $this->$parameter || '' === $this->$parameter) {
                throw new RequiredParameterMissingException($parameter);
            }
        }
    }

    private function generateSignature(): string
    {
        return \base64_encode($this->signatureGenerator->base(
            $this->merchantId,
            $this->terminalId,
            $this->id,
            $this->amount
        ));
    }
}
