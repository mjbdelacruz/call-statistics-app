<?php

namespace App\Entity;

class CallStatistics
{
    private int $customerId;

    private int $callsCountWithinSameContinent;

    private int $callsDurationWithinSameContinent;

    private int $totalNumberOfCalls;

    private int $totalDurationOfCalls;

    /**
     * @param int $customerId
     * @param int $callsCountWithinSameContinent
     * @param int $callsDurationWithinSameContinent
     * @param int $totalNumberOfCalls
     * @param int $totalDurationOfCalls
     */
    public function __construct(int $customerId, int $callsCountWithinSameContinent, int $callsDurationWithinSameContinent, int $totalNumberOfCalls, int $totalDurationOfCalls)
    {
        $this->customerId = $customerId;
        $this->callsCountWithinSameContinent = $callsCountWithinSameContinent;
        $this->callsDurationWithinSameContinent = $callsDurationWithinSameContinent;
        $this->totalNumberOfCalls = $totalNumberOfCalls;
        $this->totalDurationOfCalls = $totalDurationOfCalls;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCallsCountWithinSameContinent(): int
    {
        return $this->callsCountWithinSameContinent;
    }

    public function setCallsCountWithinSameContinent(int $callsCountWithinSameContinent): void
    {
        $this->callsCountWithinSameContinent = $callsCountWithinSameContinent;
    }

    public function getCallsDurationWithinSameContinent(): int
    {
        return $this->callsDurationWithinSameContinent;
    }

    public function setCallsDurationWithinSameContinent(int $callsDurationWithinSameContinent): void
    {
        $this->callsDurationWithinSameContinent = $callsDurationWithinSameContinent;
    }

    public function getTotalNumberOfCalls(): int
    {
        return $this->totalNumberOfCalls;
    }

    public function setTotalNumberOfCalls(int $totalNumberOfCalls): void
    {
        $this->totalNumberOfCalls = $totalNumberOfCalls;
    }

    public function getTotalDurationOfCalls(): int
    {
        return $this->totalDurationOfCalls;
    }

    public function setTotalDurationOfCalls(int $totalDurationOfCalls): void
    {
        $this->totalDurationOfCalls = $totalDurationOfCalls;
    }
}