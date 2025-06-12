<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Message;

use Psr\Http\Message\UploadedFileInterface;

class UploadedFiles
{
    protected array $uploadedFiles = [];

    public function __construct(array $uploadedFiles)
    {
        foreach ($uploadedFiles as $uploadedFile) {
            $this->add($uploadedFile);
        }
    }
    
    public function all(): array
    {
        return $this->uploadedFiles;
    }
    
    protected function add(UploadedFileInterface $uploadedFile): void
    {
        $this->uploadedFiles[] = $uploadedFile;
    }
}
