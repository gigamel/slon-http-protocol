<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\ClientMessage\Method;
use Slon\Http\Protocol\Stream\PhpInputStream;

use function assert;
use function in_array;
use function is_string;

class Request extends AbstractMessage implements RequestInterface
{
    protected UriInterface $uri;

    protected string $method;
    
    protected string $requestTarget = '';

    public function __construct(
        string $method,
        string|UriInterface $uri,
        array $headers = [],
        ?StreamInterface $body = null,
        string $protocolVersion = Version::HTTP_1_1,
    ) {
        if (is_string($uri)) {
            $this->uri = new Uri($uri);
        }
        
        $this->setMethod($method);
        
        parent::__construct(
            $body ?? new PhpInputStream(),
            $headers,
            $protocolVersion,
        );
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
        if ($this->requestTarget === $requestTarget) {
            return $this;
        }
        
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
        if ($this->method === $method) {
            return $this;
        }
        
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
        if ($this->getUri() === $uri) {
            return $this;
        }
        
        $cloned = clone $this;
        if ($preserveHost) {
            $cloned->uri = $uri->withHost($this->getUri()->getHost());
        } else {
            $cloned->uri = $uri;
        }
        
        return $cloned;
    }
    
    protected function setMethod(string $method): void
    {
        assert(in_array($method, Method::ALLOWED, true));
        $this->method = $method;
    }
}
