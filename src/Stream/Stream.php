<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Stream;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    protected string $resource;
    
    protected $stream;
    
    protected ?array $metaData = null;
    
    protected ?string $cached = null;

    public function __construct(string $resource, string $mode)
    {
        $this->resource = $resource;
        
        if (!$this->stream = \fopen($resource, $mode)) {
            throw new \RuntimeException(
                \sprintf(
                    'Resource "%s" can not opened with mode "%s"',
                    $resource,
                    $mode,
                ),
            );
        };
    }
    
    public function __toString(): string
    {
        return $this->getContents();
    }
    
    public function close(): void
    {
        \fclose($this->stream);
    }
    
    public function detach()
    {
        return null; // Todo
    }
    
    public function getSize(): ?int
    {
        return \fstat($this->stream)['size'] ?? null;
    }
    
    public function tell(): int
    {
         return \ftell($this->stream);
    }
    
    public function eof(): bool
    {
        return \feof($this->stream);
    }
    
    public function isSeekable(): bool
    {
        return true === $this->getMetadata('seekable');
    }
    
    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException(
                \sprintf('Resource "%s" can not be seek', $this->resource),
            );
        }
        
        \fseek($this->stream, $offset, $whence);
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
            $this->clearCache();
            return \fwrite($this->stream, $string);
        }
        
        throw new \RuntimeException(
            \sprintf('Resource "%s" can not be written', $this->resource),
        );
    }
    
    public function isReadable(): bool
    {
        return $this->modeContains('r')
            || $this->modeContains('b')
            || $this->modeContains('w+')
            || $this->modeContains('a+')
            || $this->modeContains('x+')
            || $this->modeContains('c+');
    }
    
    public function read(int $length): string
    {
        if ($this->isReadable()) {
            return \fread($this->stream, $length);
        }
        
        throw new \RuntimeException(
            \sprintf('Resource "%s" can not be read', $this->resource),
        );
    }
    
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException(
                \sprintf('Resource "%s" can not be read', $this->resource),
            );
        }
        
        if (null === $this->cached) {
            $buffer = '';
            while (!$this->eof()) {
                $buffer .= \fgets($this->stream);
            }
            
            $this->cached = $buffer;
        }
        
        return $this->cached;
    }
    
    public function getMetadata(?string $key = null)
    {
        if (null === $this->metaData) {
            $this->metaData = \stream_get_meta_data($this->stream);
        }
        
        return $this->metaData[$key] ?? null;
    }
    
    protected function clearCache(): void
    {
        $this->cached = null;
    }
    
    protected function modeContains(string $mode): bool
    {
        return \str_contains($this->getMetadata('mode'), $mode);
    }
}
