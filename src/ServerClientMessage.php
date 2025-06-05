<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slon\Http\Protocol\ServerMessage\ServerParams;
use Slon\Http\Protocol\ServerMessage\UploadedFiles;

class ServerClientMessage extends ClientMessage implements ServerRequestInterface
{
    protected ServerParams $queryParams;
    
    protected ServerParams $serverParams;
    
    protected ServerParams $cookieParams;
    
    protected UploadedFiles $uploadedFiles;
    
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
        
        $this->queryParams = new ServerMessage($queryParams);
        $this->serverParams = new ServerMessage($serverParams);
        $this->cookieParams = new ServerMessage($cookieParams);
        $this->uploadedFiles = new UploadedFiles($uploadedFiles);
    }
    
    public function getAttribute(string $name, $default = null): mixed
    {
        return $default; // Todo
    }
    
    public function getAttributes(): array
    {
        return []; // Todo
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
        return $this; // Todo
    }
    
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        return $this; // Todo
    }
    
    public function withParsedBody($data): ServerRequestInterface
    {
        return $this;
    }
    
    public function withQueryParams(array $query): ServerRequestInterface
    {
        return $this; // Todo
    }
    
    public function withUploadedFiles(
        array $uploadedFiles,
    ): ServerRequestInterface {
        return $this; // Todo
    }
    
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        return $this; // Todo
    }
}
