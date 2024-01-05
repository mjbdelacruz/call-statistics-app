<?php

namespace App\Entity;

class PhoneNumber
{
    private string $phoneNumber;

    private string $country;

    private string $continent;

    /**
     * @param String $phoneNumber
     * @param String $country
     * @param String $continent
     */
    public function __construct(string $phoneNumber, string $country, string $continent)
    {
        $this->phoneNumber = $phoneNumber;
        $this->country = $country;
        $this->continent = $continent;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getContinent(): string
    {
        return $this->continent;
    }

    public function setContinent(string $continent): void
    {
        $this->continent = $continent;
    }
}