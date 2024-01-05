<?php

namespace App\Repository;

use App\Entity\PhoneNumber;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class PhoneRepository extends BaseRepository
{
    private const TABLE_NAME = "phone";

    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $masterRegistry, LoggerInterface $logger)
    {
        parent::__construct($masterRegistry);
        $this->logger = $logger;
    }

    public function bulkInsert(array $phoneNumbers)
    {
        $connection = $this->getWriteConnection();
        $connection->beginTransaction();
        try {
            $sql = sprintf('INSERT INTO %s (`country`, `phone`, `continent`) VALUES ', self::TABLE_NAME);
            /** @var PhoneNumber $number*/
            foreach ($phoneNumbers AS $number) {
                $sql .= sprintf('("%s", "%s", "%s")', $number->getCountry(), $number->getPhoneNumber(), $number->getContinent());
                if ($number === end($phoneNumbers)) {
                    $sql .= ";";
                } else {
                    $sql .= ",";
                }
            }

            $connection->executeQuery($sql);
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
            $this->logger->error("Exception has occured while inserting phone numbers.", $exception->getTrace());

            throw $exception;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getContinentToPhoneMap(): array
    {
        $connection = $this->getReadConnection();
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select('p.phone AS phone, p.continent AS continent')
            ->from(self::TABLE_NAME, 'p')
            ->orderBy('continent', 'DESC');

        $phoneToContinentMap = [];
        $results = $this->fetchAll($queryBuilder);
        foreach ($results AS $result) {
            $phoneToContinentMap[$result['continent']][] = $result['phone'];
        }

        return $phoneToContinentMap;
    }
}