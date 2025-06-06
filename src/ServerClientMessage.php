<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slon\Http\Protocol\Message\Params;
use Slon\Http\Protocol\Message\QueryParams;
use Slon\Http\Protocol\ServerMessage\UploadedFiles;

class ServerClientMessage extends ClientMessage implements ServerRequestInterface
{
    protected QueryParams $queryParams;
    
    protected Params $serverParams;
    
    protected Params $cookieParams;
    
    protected UploadedFiles $uploadedFiles;
    
    protected Params $attributes;
    
    protected array|object|null $parsedBody = null;

    public function __construct(
        string $uri,
        string $method,
        array $headers = [],
        array $queryParams = [],
        array $serverParams = [],
        array $cookieParams = [],
        array $uploadedFiles = [],
        ?StreamInterface $body = null,
        string $protocolVersion = Version::HTTP_1_1,
    ) {
        parent::__construct($uri, $method, $body, $headers, $protocolVersion);
        
        $this->queryParams = new QueryParams($queryParams);
        $this->serverParams = new Params($serverParams);
        $this->cookieParams = new Params($cookieParams);
        $this->uploadedFiles = new UploadedFiles($uploadedFiles);
        $this->attributes = new Params();
    }
    
    public function getAttribute(string $name, $default = null): mixed
    {
        return $this->attributes->get($name, $default);
    }
    
    public function getAttributes(): array
    {
        return $this->attributes->all();
    }
    
    public function getCookieParams(): array
    {
        return $this->cookieParams->all();
    }
    
    public function getParsedBody()
    {
        return null; // Todo
    }
    
    public function getQueryParams(): array
    {
        return $this->queryParams->all();
    }
    
    public function getServerParams(): array
    {
        return $this->serverParams->all();
    }
    
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles->all();
    }
    
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        if ($this->attributes->get($name) === $value) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->attributes->add($name, $value);
        return $cloned;
    }
    
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        if ($this->cookieParams->all() === $cookies) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->cookieParams = new Params($cookies);
        return $cloned;
    }
    
    public function withParsedBody($data): ServerRequestInterface
    {
        return $this; // Todo
    }
    
    public function withQueryParams(array $query): ServerRequestInterface
    {
        if ($this->queryParams->all() === $query) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->queryParams = new QueryParams($query);
        return $cloned;
    }
    
    public function withUploadedFiles(
        array $uploadedFiles,
    ): ServerRequestInterface {
        if ($this->uploadedFiles->all() === $uploadedFiles) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->uploadedFiles = new UploadedFiles($uploadedFiles);
        return $cloned;
    }
    
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        return $this; // Todo
    }
}
