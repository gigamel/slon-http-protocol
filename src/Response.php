<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Slon\Http\Protocol\Enum\Code;
use Slon\Http\Enum\Version;

use function assert;
use function in_array;

class Response extends AbstractMessage implements ResponseInterface
{
    protected int $statusCode;
    
    protected string $reasonPhrase;

    public function __construct(
        string $content = '',
        int $statusCode = Code::OK,
        array $headers = [],
        string $protocolVersion = Version::HTTP_1_1,
        ?StreamInterface $body = null,
    ) {
        if (!$body instanceof StreamInterface) {
            $body = new Stream('php://temp', 'w+');
        }
        
        $body->write($content);
        $body->rewind();
        
        parent::__construct($body, $headers, $protocolVersion);
        
        $this->setStatusCode($statusCode);
        $this->setReasonPhrase(Code::TEXT[$this->statusCode] ?? 'Unknown');
    }
    
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    public function withStatus(
        int $code,
        string $reasonPhrase = '',
    ): ResponseInterface {
        if ($this->statusCode === $code) {
            $this->setReasonPhrase($reasonPhrase);
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->setReasonPhrase($reasonPhrase);
        return $cloned;
    }
    
    protected function setStatusCode(int $statusCode): void
    {
        assert(in_array($statusCode, Code::ALL, true));
        $this->statusCode = $statusCode;
    }
    
    protected function setReasonPhrase(string $reasonPhrase): void
    {
        if (!$reasonPhrase) {
            return;
        }
        
        $this->reasonPhrase = $reasonPhrase;
    }
}
