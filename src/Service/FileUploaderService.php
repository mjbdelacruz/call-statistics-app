<?php

namespace App\Service;

use App\Entity\FileUpload;
use App\Repository\FileUploadRepository;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    private SluggerInterface $slugger;

    private FileUploadRepository $fileUploadRepository;

    private LoggerInterface $logger;

    private string $uploadDir;

    public function __construct(SluggerInterface $slugger, FileUploadRepository $fileUploadRepository, LoggerInterface $logger, string $uploadDir)
    {
        $this->slugger = $slugger;
        $this->fileUploadRepository = $fileUploadRepository;
        $this->logger = $logger;
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param UploadedFile $file
     * @return FileUpload
     */
    public function upload(UploadedFile $file): FileUpload
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $sluggedFileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->uploadDir, $sluggedFileName);
        } catch (\Exception $exception) {
            $this->logger->error("Exception occured while uploading file.", $exception);
        }

        return new FileUpload($originalFilename, $sluggedFileName, $this->uploadDir);
    }

    /**
     * @param FileUpload $file
     * @return bool|int|string
     * @throws Exception
     */
    public function save(FileUpload $file): bool|int|string
    {
        return $this->fileUploadRepository->insert($file);
    }
}