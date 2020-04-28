<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

use Voronkovich\RaiffeisenBankAcquiring\AmountConverter;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidCallbackDataException;

class CallbackDataFactory
{
    private const TYPE_PAYMENT = 'conf_pay';
    private const TYPE_REVERSAL = 'conf_reversal';

    public function fromArray(array $data): CallbackData
    {
        if (!isset($data['type'])) {
            throw new InvalidCallbackDataException('Callback parameter "type" is not defined.');
        }

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
}
