<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use const FILTER_VALIDATE_URL;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

use function is_int;
use function parse_url;

class Uri implements UriInterface
{
    protected string $scheme = '';
    
    protected string $host = '';
    
    protected ?int $port = null;

    protected string $authority = '';
    
    protected string $userInfo = '';
    
    protected string $path;
    
    protected string $query = '';
    
    protected string $fragment = '';

    public function __construct(string $uri)
    {
        $chunks = parse_url($uri);
        
        if (isset($chunks['scheme'])) {
            $this->scheme = $chunks['scheme'] . '://';
        }
        
        if (isset($chunks['user'])) {
            $this->userInfo = $chunks['user'];
            
            if (isset($chunks['pass'])) {
                $this->userInfo .= ':' . $chunks['pass'];
            }
        }
        
        if (isset($chunks['host'])) {
            $this->host = $chunks['host'];
        }
        
        if (isset($chunks['port'])) {
            $this->port = (int) $chunks['port'];
        }
        
        $this->path = $chunks['path'];
        
        if (isset($chunks['query'])) {
            $this->query = '?' . $chunks['query'];
        }
        
        if (isset($chunks['fragment'])) {
            $this->fragment = '#' . $chunks['fragment'];
        }
        
        if ($this->userInfo) {
            $this->authority .= $this->userInfo . '@';
        }
        
        $this->authority .= $this->host;
        
        if (is_int($this->port)) {
            $this->authority .= ':' . $this->port;
        }
    }
    
    public function getScheme(): string
    {
        return $this->scheme;
    }
    
    public function getAuthority(): string
    {
        return $this->authority;
    }
    
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }
    
    public function getHost(): string
    {
        return $this->host;
    }
    
    public function getPort(): ?int
    {
        return $this->port;
    }
    
    public function getPath(): string
    {
        return $this->path;
    }
    
    public function getQuery(): string
    {
        return $this->query;
    }
    
    public function getFragment(): string
    {
        return $this->fragment;
    }
    
    public function withScheme(string $scheme): UriInterface
    {
        if ($this->scheme === $scheme) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->scheme = $scheme;
        return $cloned;
    }
    
    public function withUserInfo(
        string $user,
        ?string $password = null,
    ): UriInterface {
        $userInfo = $user . ($password ? ':' . $password : '');
        if ($this->userInfo === $userInfo) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->userInfo = $userInfo;
        return $cloned;
    }
    
    public function withHost(string $host): UriInterface
    {
        if ($this->host === $host) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->host = $host;
        return $cloned;
    }
    
    public function withPort(?int $port): UriInterface
    {
        if ($this->port === $port) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->port = $port;
        return $cloned;
    }
    
    public function withPath(string $path): UriInterface
    {
        if ($this->path === $path) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->path = $path;
        return $cloned;
    }
    
    public function withQuery(string $query): UriInterface
    {
        if ($this->query === $query) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->query = $query;
        return $cloned;
    }
    
    public function withFragment(string $fragment): UriInterface
    {
        if ($this->fragment === $fragment) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->fragment = $fragment;
        return $cloned;
    }
    
    public function __toString(): string
    {
        return sprintf(
            '%s%s%s%s',
            $this->scheme,
            $this->authority,
            $this->path,
            $this->query,
            $this->fragment,
        );
    }
}
