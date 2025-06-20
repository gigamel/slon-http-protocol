<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Slon\Http\Protocol\Enum\Version;
use Slon\Http\Protocol\Message\Headers;

use function assert;
use function in_array;

trait MessageTrait
{
    protected StreamInterface $body;

    protected Headers $headers;
    
    protected string $protocolVersion;
    
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function getHeader(string $name): array
    {
        return $this->headers->get($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headers->getLine($name);
    }

    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $cloned = clone $this;
        $cloned->headers->add($name, $value);
        return $cloned;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($this->getBody() === $body) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->body = $body;
        return $cloned;
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $cloned = clone $this;
        $cloned->headers->set($name, $value);
        return $cloned;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($version === $this->protocolVersion) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->setProtocolVersion($version);
        return $this;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $cloned = clone $this;
        $cloned->headers->remove($name);
        return $cloned;
    }
    
    protected function setProtocolVersion(string $version): void
    {
        assert(in_array($version, Version::NUMBERS, true));
        $this->protocolVersion = $version;
    }
}
