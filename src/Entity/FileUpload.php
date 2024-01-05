<?php

namespace App\Entity;

class FileUpload
{
    private string $originalName;

    private string $sluggedName;

    private string $uploadPath;

    /**
     * @param int $id
     * @param string $originalName
     * @param string $sluggedName
     * @param string $uploadPath
     */
    public function __construct(string $originalName, string $sluggedName, string $uploadPath)
    {
        $this->originalName = $originalName;
        $this->sluggedName = $sluggedName;
        $this->uploadPath = $uploadPath;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getSluggedName(): string
    {
        return $this->sluggedName;
    }

    public function setSluggedName(string $sluggedName): void
    {
        $this->sluggedName = $sluggedName;
    }

    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }

    public function setUploadPath(string $uploadPath): void
    {
        $this->uploadPath = $uploadPath;
    }
}