<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\Enum\Method;

use function assert;
use function in_array;

trait RequestTrait
{
    protected UriInterface $uri;

    protected string $method;
    
    protected ?string $requestTarget = null;
    
    public function getRequestTarget(): string
    {
        if (null === $this->requestTarget) {
            $this->requestTarget = $this->uri->getPath() ?: '/';
            
            if ($this->uri->getQuery()) {
                $this->requestTarget .= $this->uri->getQuery();
            }
            
            if ($this->uri->getFragment()) {
                $this->requestTarget .= $this->uri->getFragment();
            }
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
