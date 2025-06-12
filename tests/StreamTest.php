<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Slon\Http\Protocol\Stream;

class StreamTest extends TestCase
{
    public static function modeDataProvider(): array
    {
        return [
            [
                'resource' => 'php://temp',
                'mode' => 'w+b',
                'isReadable' => true,
                'isWritable' => true,
            ],
            [
                'resource' => 'php://output',
                'mode' => 'wb',
                'isReadable' => false,
                'isWritable' => true,
                'isSeekable' => false,
            ],
            [
                'resource' => 'php://input',
                'mode' => 'rb',
                'isReadable' => true,
                'isWritable' => false,
            ],
        ];
    }
    
    public static function readRuntimeDataProvider(): array
    {
        return [
            [
                'resource' => 'php://temp',
                'mode' => 'w+b',
            ],
            [
                'resource' => 'php://output',
                'mode' => 'wb',
                'isReadable' => false,
            ],
            [
                'resource' => 'php://input',
                'mode' => 'rb',
            ],
        ];
    }
    
    public static function writeRuntimeDataProvider(): array
    {
        return [
            [
                'resource' => 'php://temp',
                'mode' => 'w+b',
            ],
            [
                'resource' => 'php://output',
                'mode' => 'wb',
            ],
            [
                'resource' => 'php://input',
                'mode' => 'rb',
                'isWritable' => false,
            ],
        ];
    }
    
    public static function seekRuntimeDataProvider(): array
    {
        return [
            [
                'resource' => 'php://temp',
                'mode' => 'w+b',
            ],
            [
                'resource' => 'php://output',
                'mode' => 'wb',
                'isSeekable' => false,
            ],
            [
                'resource' => 'php://input',
                'mode' => 'rb',
            ],
        ];
    }
    
    #[DataProvider('modeDataProvider')]
    public function testMode(
        string $resource,
        string $mode,
        bool $isReadable,
        bool $isWritable,
        bool $isSeekable = true,
    ): void {
        $stream = $this->makeStream($resource, $mode);
        
        $this->assertEquals($stream->isReadable(), $isReadable);
        $this->assertEquals($stream->isWritable(), $isWritable);
        $this->assertEquals($stream->isSeekable(), $isSeekable);
        
        $stream->close();
    }
    
    #[DataProvider('readRuntimeDataProvider')]
    public function testReadRuntimeException(
        string $resource,
        string $mode,
        bool $isReadable = true,
    ): void {
        if (!$isReadable) {
            $this->expectException(RuntimeException::class);
        }
        
        $stream = $this->makeStream($resource, $mode);
        
        $this->assertEquals($stream->isReadable(), $isReadable);
        
        $stream->read(1);
        $stream->close();
    }
    
    #[DataProvider('writeRuntimeDataProvider')]
    public function testWriteRuntimeException(
        string $resource,
        string $mode,
        bool $isWritable = true,
    ): void {
        if (!$isWritable) {
            $this->expectException(RuntimeException::class);
        }
        
        $stream = $this->makeStream($resource, $mode);
        
        $this->assertEquals($stream->isWritable(), $isWritable);
        
        $stream->write('');
        $stream->close();
    }
    
    #[DataProvider('seekRuntimeDataProvider')]
    public function testSeekRuntimeException(
        string $resource,
        string $mode,
        bool $isSeekable = true,
    ): void {
        if (!$isSeekable) {
            $this->expectException(RuntimeException::class);
        }
        
        $stream = $this->makeStream($resource, $mode);
        
        $this->assertEquals($stream->isSeekable(), $isSeekable);
        
        $stream->rewind();
        $stream->close();
    }
    
    private function makeStream(
        string $resource,
        string $mode,
    ): StreamInterface {
        return new Stream($resource, $mode);
    }
}
