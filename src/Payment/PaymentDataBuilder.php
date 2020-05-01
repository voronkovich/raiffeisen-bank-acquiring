<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Payment;

use Voronkovich\RaiffeisenBankAcquiring\AmountConverter;
use Voronkovich\RaiffeisenBankAcquiring\Exception\RequiredParameterMissingException;
use Voronkovich\RaiffeisenBankAcquiring\Signature\Signature;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SignatureGenerator;

class PaymentDataBuilder
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
    private $language;
    private $lifetime;
    private $requireCardholderName = false;
    private $requireCardholderEmail = false;
    private $requireCardholderPhone = false;
    private $requireCardholderCountry = false;
    private $requireCardholderCity = false;

    private $signatureGenerator;
    private $signatureEncoding;

    public function __construct(
        SignatureGenerator $signatureGenerator = null,
        string $signatureEncoding = Signature::BASE64
    ) {
        $this->signatureGenerator = $signatureGenerator;
        $this->signatureEncoding = $signatureEncoding;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setAmount(int $amount): self
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

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function setLifetime(int $lifetimeInSeconds): self
    {
        $this->lifetime = $lifetimeInSeconds;

        return $this;
    }

    public function requireCardholderName(bool $requireCardholderName = true): self
    {
        $this->requireCardholderName = $requireCardholderName;

        return $this;
    }

    public function requireCardholderEmail(bool $requireCardholderEmail = true): self
    {
        $this->requireCardholderEmail = $requireCardholderEmail;

        return $this;
    }

    public function requireCardholderPhone(bool $requireCardholderPhone = true): self
    {
        $this->requireCardholderPhone = $requireCardholderPhone;

        return $this;
    }

    public function requireCardholderCountry(bool $requireCardholderCountry = true): self
    {
        $this->requireCardholderCountry = $requireCardholderCountry;

        return $this;
    }

    public function requireCardholderCity(bool $requireCardholderCity = true): self
    {
        $this->requireCardholderCity = $requireCardholderCity;

        return $this;
    }

    public function getData(): array
    {
        $this->checkRequiredParameters();

        $data = [
            'PurchaseDesc' => $this->id,
            'PurchaseAmt' => AmountConverter::forPayment()->minorToFormatted($this->amount),
            'MerchantID' => \sprintf('00000%s-%s', $this->merchantId, $this->terminalId),
            'MerchantName' => $this->merchantName,
            'CountryCode' => $this->merchantCountry,
            'CurrencyCode' => $this->merchantCurrency,
            'MerchantCity' => $this->merchantCity,
            'MerchantURL' => $this->merchantUrl,
            'SuccessURL' => $this->successUrl,
            'FailURL' => $this->failUrl,
        ];


        $this->addLanguageIfNeeded($data);
        $this->addLifetimeIfNeeded($data);
        $this->requireCardholderInfoIfNeeded($data);

        $this->addSignature($data);

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

    private function addLanguageIfNeeded(array &$data): void
    {
        if (null !== $this->language) {
            $data['Language'] = Language::fromIsoCode($this->language);
        }
    }

    private function addLifetimeIfNeeded(array &$data): void
    {
        if (null !== $this->lifetime) {
            $data['Window'] = $this->lifetime;
            $data['Time'] = \time();
            $data['Options'] = 'T'.($data['Options'] ?? '');
        }
    }

    private function requireCardholderInfoIfNeeded(array &$data): void
    {
        if ($this->requireCardholderName) {
            $data['CardholderName'] = 'Y';
        }

        if ($this->requireCardholderEmail) {
            $data['Email'] = 'Y';
        }

        if ($this->requireCardholderPhone) {
            $data['Phone'] = 'Y';
        }

        if ($this->requireCardholderCountry) {
            $data['Country'] = 'Y';
        }

        if ($this->requireCardholderCity) {
            $data['City'] = 'Y';
        }
    }

    private function addSignature(array &$data): void
    {
        if (null !== $this->signatureGenerator) {
            $signature = $this->generateSignature($data);
            $data['HMAC'] = $signature->getValue($this->signatureEncoding);

            if (Signature::HEX === $this->signatureEncoding) {
                $data['Options'] = 'H'.($data['Options'] ?? '');
            }
        }
    }

    private function generateSignature(array $data): Signature
    {
        $chunks = [
            $this->merchantId,
            $this->terminalId,
            $data['PurchaseDesc'],
            $data['PurchaseAmt'],
        ];

        if (isset($data['Time']) && isset($data['Window'])) {
            $chunks[] = $data['Time'];
            $chunks[] = $data['Window'];
        }

        return $this->signatureGenerator->generate($chunks);
    }
}
