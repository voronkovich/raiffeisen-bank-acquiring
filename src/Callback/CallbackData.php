<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

abstract class CallbackData
{
    public const SUCCESS = '0';

    private $id;
    private $amount;
    private $transactionId;
    private $date;
    private $result;

    public function __construct(
        string $id,
        int $amount,
        string $transactionId,
        \DateTime $date,
        string $result
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
        $this->date = $date;
        $this->result = $result;
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

    public function getResult(): string
    {
        return $this->result;
    }

    public function isSuccessfull(): bool
    {
        return self::SUCCESS === $this->result;
    }
}
