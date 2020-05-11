<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Callback;

class CardholderData
{
    private $firstName;
    private $lastName;
    private $email;
    private $phone;
    private $country;
    private $city;
    private $address;

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $country = null,
        ?string $city = null,
        ?string $address = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->country = $country;
        $this->city = $city;
        $this->address = $address;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }
}
