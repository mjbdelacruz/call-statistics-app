<?php

namespace App\Repository;

use App\Entity\FileUpload;
use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class FileUploadRepository extends BaseRepository
{
    private const TABLE_NAME = "uploaded_file";

    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $masterRegistry, LoggerInterface $logger)
    {
        parent::__construct($masterRegistry);
        $this->logger = $logger;
    }

    /**
     * @param FileUpload $file
     * @return bool|int|string
     * @throws DoctrineException
     */
    public function insert(FileUpload $file): bool|int|string
    {
        $connection = $this->getWriteConnection();
        try {
            $connection->insert(self::TABLE_NAME, [
                'original_name' => $file->getOriginalName(),
                'slugged_name' => $file->getSluggedName(),
                'upload_path' => $file->getUploadPath()
            ]);

            return $connection->lastInsertId();
        } catch (DoctrineException|\Exception $e) {
            $this->logger->error('Exception has occured while inserting FileUpload info.', $e);
            throw $e;
        }
    }

    public function createFromArray(array $data)
    {
        return new FileUpload(
            $data['original_name'],
            $data['slugged_name'],
            $data['upload_path']
        );
    }
}