<?php

namespace App\Entity;

class CallLog
{
    private int $customerId;

    private string $datetime;

    private int $duration;

    private string $dialedPhone;

    private string $ip;

    /**
     * @param int $customerId
     * @param string $datetime
     * @param int $duration
     * @param string $dialedPhone
     * @param string $ip
     */
    public function __construct(int $customerId, string $datetime, int $duration, string $dialedPhone, string $ip)
    {
        $this->customerId = $customerId;
        $this->datetime = $datetime;
        $this->duration = $duration;
        $this->dialedPhone = $dialedPhone;
        $this->ip = $ip;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getDatetime(): string
    {
        return $this->datetime;
    }

    public function setDatetime(string $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getDialedPhone(): string
    {
        return $this->dialedPhone;
    }

    public function setDialedPhone(string $dialedPhone): void
    {
        $this->dialedPhone = $dialedPhone;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }
}