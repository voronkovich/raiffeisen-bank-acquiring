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

    public function __construct(SignatureGenerator $signatureGenerator = null)
    {
        $this->signatureGenerator = $signatureGenerator;
    }

    public function fromArray(array $data): CallbackData
    {
        if (!isset($data['type'])) {
            throw new InvalidCallbackDataException('Callback parameter "type" is not defined.');
        }

        $this->checkSignature($data);

        $amount = AmountConverter::forCallback()->formattedToMinor($data['amt']);

        switch ($data['type']) {
            case self::TYPE_PAYMENT:
                return new CallbackPaymentData(
                    $data['descr'],
                    $amount,
                    $data['id'],
                    new \DateTime($data['date']),
                    $data['result']
                );
                break;
            case self::TYPE_REVERSAL:
                return new CallbackReversalData(
                    $data['descr'],
                    $amount,
                    $data['id'],
                    new \DateTime($data['date']),
                    $data['result']
                );
                break;
            default:
                break;
        }
    }

    private function checkSignature(array $data): void
    {
        if (isset($data['hmac']) && null !== $this->signatureGenerator) {
            $signature = $this->signatureGenerator->callback($data['descr'], $data['amt'], $data['result']);

            if ($signature->getValue() !== $data['hmac']) {
                throw new InvalidCallbackSignatureException(\sprintf(
                    'Payment with ID "%s" has invalid signature.',
                    $data['descr']
                ));
            }
        }
    }
}
