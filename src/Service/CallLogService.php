<?php

namespace App\Service;

use App\Entity\CallLog;
use App\Repository\CallLogRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\File\File;

class CallLogService
{
    private CallLogRepository $callLogRepository;

    /**
     * @param CallLogRepository $callLogRepository
     */
    public function __construct(CallLogRepository $callLogRepository)
    {
        $this->callLogRepository = $callLogRepository;
    }


    /**
     * @param File $file
     * @return array
     */
    public function parse(File $file): array
    {
        $callLogs = [];
        // Open the file
        if (($fileHandle = fopen($file->getPathname(), "r")) !== false) {
            while (($data = fgetcsv($fileHandle)) !== false) {
                if (count($data) != 5) {
                    return [];
                }
                $customerId = $data[0];
                $callDate = $data[1];
                $duration = $data[2];
                $dialedPhone = $data[3];
                $ipAddress = $data[4];

                $callLogs[] = new CallLog(
                    $customerId,
                    $callDate,
                    $duration,
                    $dialedPhone,
                    $ipAddress
                );
            }

            fclose($fileHandle);
        }

        return $callLogs;
    }

    /**
     * @param $callLogs
     * @return void
     * @throws \Exception
     */
    public function save($callLogs): void
    {
        $this->callLogRepository->bulkInsert($callLogs);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws NoResultException
     */
    public function getPollingInfo()
    {
        return $this->callLogRepository->getPollingInfo();
    }
}