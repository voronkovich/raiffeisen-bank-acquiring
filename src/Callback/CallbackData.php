<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

/**
 * Base class for all callback data types.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
abstract class CallbackData
{
    private $id;
    private $amount;
    private $transactionId;
    private $date;
    private $errorCode;
    private $errorMessage;

    public function __construct(
        string $id,
        int $amount,
        string $transactionId,
        \DateTime $date,
        ?int $errorCode,
        ?string $errorMessage
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
        $this->date = $date;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isSuccessfull(): bool
    {
        return null === $this->errorCode;
    }
}
