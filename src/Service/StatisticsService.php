<?php

namespace App\Service;

use App\Entity\CallLog;
use App\Entity\CallStatistics;
use App\Exception\ServiceException;
use App\Repository\CallLogRepository;
use App\Repository\PhoneRepository;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;

class StatisticsService
{
    private CallLogRepository $callLogRepository;

    private GeoLocationApiService $geoLocationApiService;

    private PhoneRepository $phoneRepository;

    private LoggerInterface $logger;

    /**
     * @param CallLogRepository $callLogRepository
     */
    public function __construct(CallLogRepository $callLogRepository, PhoneRepository $phoneRepository, GeoLocationApiService $geoLocationApiService, LoggerInterface $logger)
    {
        $this->callLogRepository = $callLogRepository;
        $this->phoneRepository = $phoneRepository;
        $this->geoLocationApiService = $geoLocationApiService;
        $this->logger = $logger;
    }

    /**
     * @return array
     * @throws ServiceException
     * @throws Exception
     */
    public function getStatistics(): array
    {
        // Fetch unique IP Addresses from the database
        $ipAddresses = $this->callLogRepository->getDistinctIpAddresses();

        // Get continent of IP Addresses via GeoLocation API
        $ipAddressesToContinentMap = $this->geoLocationApiService->getContinentsByIpAddresses($ipAddresses);

        // Get continent of Phone
        $continentToPhoneMap = $this->phoneRepository->getContinentToPhoneMap();

        // Get call logs
        $callLogs = $this->callLogRepository->getCallLogs();

        $statistics = [];
        // Iterate through call logs grouped by customer id to build the report
        foreach ($callLogs AS $customerId => $logs) {
            $totalCall = 0;
            $totalDuration = 0;
            $totalDurationWithinSameContinent = 0;
            $totalCountWithinSameContinent = 0;
            /** @var CallLog $customerLog */
            foreach ($logs AS $customerLog) {
                $totalCall++;
                $totalDuration += $customerLog->getDuration();

                // Continent of IP Address
                $ipContinent = $ipAddressesToContinentMap[$customerLog->getIp()];

                // filter phones by continent from IP
                $phonesByContinent = $continentToPhoneMap[$ipContinent];
                foreach ($phonesByContinent AS $phoneSuffix) {
                    // Check if any phone suffix will match dialed phone number
                    if (str_starts_with($customerLog->getDialedPhone(), $phoneSuffix)) {
                        $totalCountWithinSameContinent++;
                        $totalDurationWithinSameContinent += $customerLog->getDuration();
                        break;
                    }
                }
            }

            $statistics[] = new CallStatistics(
                $customerId,
                $totalCountWithinSameContinent,
                $totalDurationWithinSameContinent,
                $totalCall,
                $totalDuration
            );
        }

        return $statistics;
    }
}