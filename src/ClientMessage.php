<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\ClientMessage\Method;
use Slon\Http\Protocol\Stream\PhpInputStream;

class ClientMessage implements RequestInterface
{
    protected UriInterface $uri;

    protected HeadersInterface $headers;
    
    protected StreamInterface $body;

    protected string $method;

    protected string $protocolVersion;
    
    protected string $requestTarget = '';

    public function __construct(
        string $uri,
        string $method,
        ?StreamInterface $body = null,
        array $headers = [],
        string $protocolVersion = Version::HTTP_1_1,
    ) {
        $this->uri = new Uri($uri);
        $this->setMethod($method);
        $this->body = $body ?? new PhpInputStream();
        $this->headers = new Headers($headers);
        $this->setProtocolVersion($protocolVersion);
    }
    
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
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
    
    public function getHeaders(): array
    {
        return $this->headers->all();
    }
    
    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }
    
    public function getHeader(string $name): array
    {
        return $this->headers->get($name);
    }
    
    public function getHeaderLine(string $name): string
    {
        return $this->headers->getLine($name);
    }
    
    public function withHeader(
         string $name,
         $value,
    ): MessageInterface {
        $cloned = clone $this;
        $cloned->headers->set($name, $value);
        return $cloned;
    }
    
    public function withAddedHeader(
        string $name,
        $value,
    ): MessageInterface {
        $cloned = clone $this;
        $cloned->headers->add($name, $value);
        return $cloned;
    }
    
    public function withoutHeader(string $name): MessageInterface
    {
        $cloned = clone $this;
        $cloned->headers->remove($name, $value);
        return $cloned;
    }
    
    public function getBody(): StreamInterface
    {
        return $this->body;
    }
    
    public function withBody(StreamInterface $body): MessageInterface
    {
        $cloned = clone $this;
        $cloned->body = $body;
        return $cloned;
    }
    
    public function getRequestTarget(): string
    {
        if ('' === $this->requestTarget) {
            $this->requestTarget .= $this->uri->getPath();
            if ($this->uri->getQuery()) {
                $this->requestTarget .= '?' . $this->uri->getQuery();
            }
            if ($this->uri->getFragment()) {
                $this->requestTarget .= '#' . $this->uri->getFragment();
            }
            $this->requestTarget = $this->requestTarget ?: '/';
        }
        
        return $this->requestTarget;
    }
    
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $cloned = clone $this;
        $cloned->requestTarget = $requestTarget;
        return $cloned;
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function withMethod(string $method): RequestInterface
    {
        $cloned = clone $this;
        $cloned->setMethod($method);
        return $cloned;
    }
    
    public function getUri(): UriInterface
    {
        return $this->uri;
    }
    
    public function withUri(
        UriInterface $uri,
        bool $preserveHost = false,
    ): RequestInterface {
        $cloned = clone $this;
        $cloned->uri = $uri; // Todo
        return $cloned;
    }
    
    protected function setMethod(string $method): void
    {
        \assert(\in_array($method, Method::ALLOWED, true));
        $this->method = $method;
    }

    protected function setProtocolVersion(string $version): void
    {
        \assert(\in_array($version, Version::NUMBERS, true));
        $this->protocolVersion = $version;
    }
}
