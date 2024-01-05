<?php

namespace App\Repository;

use App\Entity\CallLog;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class CallLogRepository extends BaseRepository
{
    private const TABLE_NAME = "call_log";

    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $masterRegistry, LoggerInterface $logger)
    {
        parent::__construct($masterRegistry);
        $this->logger = $logger;
    }

    /**
     * @param $callLogs
     * @return void
     * @throws Exception
     */
    public function bulkInsert(array $callLogs): void
    {
        $connection = $this->getWriteConnection();
        $connection->beginTransaction();
        try {
            $sql = sprintf('INSERT INTO %s (`customer_id`, `call_date`, `duration`, `phone`, `ip`) VALUES ', self::TABLE_NAME);
            /** @var CallLog $log*/
            foreach ($callLogs AS $log) {
                $sql .= sprintf('("%s", "%s", "%s", "%s", "%s")', $log->getCustomerId(), $log->getDatetime(), $log->getDuration(), $log->getDialedPhone(), $log->getIp());
                if ($log === end($callLogs)) {
                    $sql .= ";";
                } else {
                    $sql .= ",";
                }
            }

            $connection->executeQuery($sql);
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            $this->logger->error("Exception has occured while inserting call logs.", $exception->getTrace());

            throw $exception;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getCallLogs(): array
    {
        $connection = $this->getReadConnection();
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select('cl.customer_id AS customer_id, cl.call_date AS call_date, cl.duration AS duration, cl.phone AS phone, cl.ip AS ip')
            ->from(self::TABLE_NAME, 'cl')
            ->orderBy('cl.customer_id', 'DESC');

        $callLogs = [];
        $results = $this->fetchAll($queryBuilder);
        foreach ($results AS $result) {
            $callLogs[$result['customer_id']][] = $this->createFromArray($result);
        }

        return $callLogs;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDistinctIpAddresses(): array
    {
        $connection = $this->getReadConnection();
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select('cl.ip AS ip')
            ->from(self::TABLE_NAME, 'cl')
            ->groupBy('cl.ip');

        $ipAddresses = [];
        $results = $this->fetchAll($queryBuilder);
        if (count($results) > 0) {
            $ipAddresses = array_column($results, 'ip');
        }

        return $ipAddresses;
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws NoResultException
     */
    public function getPollingInfo()
    {
        $connection = $this->getReadConnection();
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select('COUNT(*) AS total_record, MAX(`modified_datetime`) AS max_datetime')
            ->from(self::TABLE_NAME, 'cl');

        $result = $this->fetchOne($queryBuilder);
        if ($result) {
            return ['totalRecord' => $result['total_record'], 'maxDateTime' => $result['max_datetime']];
        }
    }

    public function createFromArray(array $data)
    {
        return new CallLog(
            $data['customer_id'],
            $data['call_date'],
            $data['duration'],
            $data['phone'],
            $data['ip'],
        );
    }
}