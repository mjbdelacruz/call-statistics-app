<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository
{
    /**
     * @var ManagerRegistry
     */
    private $masterRegistry;

    protected static $tableName;

    /**
     * @param ManagerRegistry $masterRegistry
     */
    public function __construct(ManagerRegistry $masterRegistry)
    {
        $this->masterRegistry = $masterRegistry;
    }

    /**
     * @return Connection
     */
    protected function getReadConnection(): Connection
    {
        return $this->masterRegistry->getConnection('read');
    }

    /**
     * @return Connection
     */
    protected function getWriteConnection(): Connection
    {
        return $this->masterRegistry->getConnection('write');
    }

    /**
     * @return mixed
     *
     * @throws NoResultException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchOne(QueryBuilder $qb)
    {
        $result = $qb->executeQuery()->fetchAssociative();
        if (!$result) {
            throw new NoResultException();
        }

        return $result;
    }

    /**
     * @return array[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fetchAll(QueryBuilder $qb)
    {
        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return void
     * @throws DoctrineException
     */
    public function beginTransaction(): void
    {
        $connection = $this->getWriteConnection();
        $connection->beginTransaction();
    }

    /**
     * @return void
     * @throws DoctrineException
     */
    protected function commitTransaction(): void
    {
        $connection = $this->getWriteConnection();
        $connection->commit();
    }

    /**
     * @return void
     * @throws DoctrineException
     */
    protected function rollBackTransaction(): void
    {
        $connection = $this->getWriteConnection();
        $connection->rollBack();
    }
}