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

    private $signatureGenerator;

    public function __construct(SignatureGenerator $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
    }

    public function fromArray(array $data): CallbackData
    {
        if (!isset($data['type'])) {
            throw new InvalidCallbackDataException('Callback parameter "type" is not defined.');
        }

        $this->checkSignature($data);

        $id = $data['descr'];
        $amount = AmountConverter::forCallback()->formattedToMinor($data['amt']);
        $transactionId = $data['id'];
        $transactionDate = new \DateTime($data['date']);
        $transactionResult = $data['result'];

        switch ($data['type']) {
            case self::TYPE_PAYMENT:
                return new CallbackPaymentData(
                    $id,
                    $amount,
                    $transactionId,
                    $transactionDate,
                    $transactionResult,
                    $this->getCardholderData($data)
                );
                break;
            case self::TYPE_REVERSAL:
                return new CallbackReversalData(
                    $id,
                    $amount,
                    $transactionId,
                    $transactionDate,
                    $transactionResult
                );
                break;
            default:
                break;
        }
    }

    private function checkSignature(array $data): void
    {
        $signature = $this->signatureGenerator->callback($data['descr'], $data['amt'], $data['result']);

        if ($signature->base64() !== $data['hmac']) {
            throw new InvalidCallbackSignatureException(\sprintf(
                'Payment with ID "%s" has invalid signature.',
                $data['descr']
            ));
        }
    }

    public function getCardholderData(array $data): ?CardholderData
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
