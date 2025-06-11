<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

use function fclose;
use function feof;
use function fgets;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function sprintf;
use function stream_get_meta_data;
use function str_contains;

class Stream implements StreamInterface
{
    protected string $resource;
    
    protected $stream;
    
    protected ?array $metaData = null;

    public function __construct(string $resource, string $mode)
    {
        if (!$this->stream = fopen($resource, $mode)) {
            throw new RuntimeException(
                sprintf(
                    'Resource "%s" can not opened with mode "%s"',
                    $resource,
                    $mode,
                ),
            );
        };
        
        $this->resource = $resource;
    }
    
    public function __toString(): string
    {
        return $this->getContents();
    }
    
    public function close(): void
    {
        if ($this->stream) {
            fclose($this->stream);
            $this->stream = null;
        }
    }
    
    public function detach()
    {
        $this->close();
        return $this->stream;
    }
    
    public function getSize(): ?int
    {
        if (!$this->stream) {
            return null;
        }
        
        return fstat($this->stream)['size'] ?? null;
    }
    
    public function tell(): int
    {
        if ($this->stream) {
            return ftell($this->stream);
        }
        
        return 0;
    }
    
    public function eof(): bool
    {
        if ($this->stream) {
            return feof($this->stream);
        }
        
        return true;
    }
    
    public function isSeekable(): bool
    {
        if ($this->stream) {
            return true === $this->getMetadata('seekable');
        }
        
        return false;
    }
    
    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            $this->close();
            
            throw new RuntimeException(
                sprintf('Resource "%s" can not be seek', $this->resource),
            );
        }
        
        fseek($this->stream, $offset, $whence);
    }
    
    public function rewind(): void
    {
        $this->seek(0);
    }
    
    public function isWritable(): bool
    {
        return $this->modeContains('r+')
            || $this->modeContains('w')
            || $this->modeContains('a')
            || $this->modeContains('x')
            || $this->modeContains('c');
    }
    
    public function write(string $string): int
    {
        if ($this->isWritable()) {
            return fwrite($this->stream, $string);
        }
        
        $this->close();
        
        throw new RuntimeException(
            sprintf('Resource "%s" can not be written', $this->resource),
        );
    }
    
    public function isReadable(): bool
    {
        return $this->modeContains('r')
            || $this->modeContains('w+')
            || $this->modeContains('a+')
            || $this->modeContains('x+')
            || $this->modeContains('c+');
    }
    
    public function read(int $length): string
    {
        if ($this->isReadable()) {
            return fread($this->stream, $length);
        }
        
        $this->close();
        
        throw new RuntimeException(
            sprintf('Resource "%s" can not be read', $this->resource),
        );
    }
    
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            $this->close();
            
            throw new RuntimeException(
                sprintf('Resource "%s" can not be read', $this->resource),
            );
        }
        
        $this->rewind();
        
        $contents = '';
        while (!$this->eof()) {
            $contents .= fgets($this->stream);
        }
        
        return $contents;
    }
    
    public function getMetadata(?string $key = null)
    {
        if (!$this->stream) {
            return null;
        }
        
        return stream_get_meta_data($this->stream)[$key] ?? null;
    }
    
    protected function modeContains(string $mode): bool
    {
        if ($this->stream) {
            return str_contains($this->getMetadata('mode') ?? '', $mode);
        }
        
        return false;
    }
}
