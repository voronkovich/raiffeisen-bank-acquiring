<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

use Voronkovich\RaiffeisenBankAcquiring\AmountConverter;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidCallbackDataException;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidCallbackSignatureException;
use Voronkovich\RaiffeisenBankAcquiring\Signature\SignatureGenerator;

class CallbackDataFactory
{
    private const TYPE_PAYMENT = 'conf_pay';
    private const TYPE_REVERSAL = 'conf_reversal';

    private const SUCCESS = '0';

    private $signatureGenerator;
    private $amountConverter;

    public function __construct(SignatureGenerator $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
        $this->amountConverter = AmountConverter::forCallback();
    }

    public function fromArray(array $data): CallbackData
    {
        if (!isset($data['type'])) {
            throw new InvalidCallbackDataException('Callback parameter "type" is not defined.');
        }

        $callbackType = $data['type'];

        $this->checkSignature($data);

        $id = $data['descr'];
        $amount = $this->amountConverter->formattedToMinor($data['camt'] ?? $data['amt']);
        $transactionId = $data['id'];
        $transactionDate = new \DateTime($data['date']);
        $errorCode = self::SUCCESS !== $data['result'] ? (int) $data['result'] : null;

        switch ($callbackType) {
            case self::TYPE_PAYMENT:
                return $this->createPaymentCallback(
                    $id,
                    $amount,
                    $transactionId,
                    $transactionDate,
                    $errorCode,
                    $data
                );
            case self::TYPE_REVERSAL:
                return $this->createReversalCallback(
                    $id,
                    $amount,
                    $transactionId,
                    $transactionDate,
                    $errorCode,
                    $data
                );
        }

        throw new InvalidCallbackDataException(\sprintf('Callback type "%s" is not supported.', $callbackType));
    }

    private function createPaymentCallback(
        string $id,
        int $amount,
        string $transactionId,
        \DateTimeInterface $transactionDate,
        ?int $errorCode,
        array $data
    ): PaymentData {
        $authorizationCode = null;
        $errorMessage = null;
        $currency = null;
        $convertedAmount = null;

        if (null === $errorCode) {
            $authorizationCode = $data['comment'];
        } else {
            $errorMessage = $data['comment'];
        }

        if (isset($data['ccode'])) {
            $currency = (int) $data['ccode'];
            $convertedAmount = $this->amountConverter->formattedToMinor($data['amt']);
        }

        $cardholder = $this->getCardholderData($data);

        return new PaymentData(
            $id,
            $amount,
            $transactionId,
            $transactionDate,
            $errorCode,
            $errorMessage,
            $authorizationCode,
            $currency,
            $convertedAmount,
            $cardholder,
            $data['ext1'] ?? null,
            $data['ext2'] ?? null
        );
    }

    private function createReversalCallback(
        string $id,
        int $amount,
        string $transactionId,
        \DateTimeInterface $transactionDate,
        ?int $errorCode,
        array $data
    ): ReversalData {
        $errorMessage = null !== $errorCode ? ReversalData::errorCodeToMessage($errorCode) : null;

        return new ReversalData(
            $id,
            $amount,
            $transactionId,
            $transactionDate,
            $errorCode,
            $errorMessage
        );
    }

    private function checkSignature(array $data): void
    {
        $signature = $this->signatureGenerator->generate([ $data['descr'], $data['amt'], $data['result'] ]);

        if ($signature->base64() !== $data['hmac']) {
            throw new InvalidCallbackSignatureException(\sprintf(
                'Payment with ID "%s" has invalid signature.',
                $data['descr']
            ));
        }
    }

    private function getCardholderData(array $data): ?CardholderData
    {
        $cardholderKeys = [ 'fn', 'ln', 'email', 'phone', 'cntr', 'city', 'address' ];
        if (!\array_intersect(\array_keys($data), $cardholderKeys)) {
            return null;
        }

        return new CardholderData(
            $data['fn'] ?? null,
            $data['ln'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['cntr'] ?? null,
            $data['city'] ?? null,
            $data['addr'] ?? null
        );
    }
}
