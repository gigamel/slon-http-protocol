<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use const UPLOAD_ERR_OK;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Slon\Http\Protocol\Stream\Stream;

use function dirname;
use function is_file;
use function is_dir;
use function sprintf;

class UploadedFile implements UploadedFileInterface
{
    protected const array ERROR_MESSAGE = [
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];

    protected StreamInterface $stream;
    
    protected int $error;

    protected ?int $size;
    
    protected ?string $clientFilename;
    
    protected ?string $clientMediaType;

    public function __construct(
        StreamInterface $stream,
        int $error,
        ?int $size = null,
        ?string $clientFilename = null,
        ?string $clientMediaType = null,
    ) {
        if (UPLOAD_ERR_OK !== $error) {
            throw new RuntimeException(sprintf(
                'Error when uploading a resource "%s" to the server. Reason: %s',
                self::ERROR_MESSAGE[$error] ?? 'Unknown error',
                $stream->getMetadata('uri'),
            ));
        }
        
        $this->stream = $stream;
        $this->error = $error;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }
    
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }
    
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
    
    public function getError(): int
    {
        return $this->error;
    }
    
    public function getSize(): ?int
    {
        return $this->size;
    }
    
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }
    
    public function moveTo(string $targetPath): void
    {
        if (is_file($targetPath)) {
            throw new RuntimeException(sprintf(
                'File "%s" already exists',
                $targetPath,
            ));
        }
        
        if (!is_dir(dirname($targetPath))) {
            throw new RuntimeException(sprintf(
                'Resource "%s" cannot be uploaded. The directory does not exist',
                $targetPath,
            ));
        }
        
        $targetStream = new Stream($targetPath, 'wb+');
        
        $this->getStream()->rewind();
        while (!$this->getStream()->eof()) {
            $targetStream->write(
                $this->getStream()->read(1024),
            );
        }
        
        $targetStream->close();
        $this->getStream()->close();
    }
}
