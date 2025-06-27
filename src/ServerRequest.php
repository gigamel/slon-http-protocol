<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\Enum\Version;
use Slon\Http\Protocol\Message\Headers;
use Slon\Http\Protocol\Message\Params;
use Slon\Http\Protocol\Message\QueryParams;
use Slon\Http\Protocol\Message\UploadedFiles;
use Slon\Streams\PhpInputStream;

use function is_array;
use function is_object;
use function is_string;

class ServerRequest implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;
    
    protected QueryParams $queryParams;
    
    protected Params $serverParams;
    
    protected Params $cookieParams;
    
    protected UploadedFiles $uploadedFiles;
    
    protected Params $attributes;
    
    protected array|object|null $parsedBody = null;

    public function __construct(
        string $method,
        string|UriInterface $uri,
        array $headers = [],
        array $queryParams = [],
        array $parsedBody = [],
        array $serverParams = [],
        array $cookieParams = [],
        array $uploadedFiles = [],
        array $attributes = [],
        string $protocolVersion = Version::HTTP_1_1,
    ) {
        $this->setMethod($method);
        $this->uri = is_string($uri) ? new Uri($uri) : $uri;
        $this->headers = new Headers($headers);
        $this->queryParams = new QueryParams($queryParams);
        $this->body = new PhpInputStream();
        $this->parsedBody = $parsedBody;
        $this->serverParams = new Params($serverParams);
        $this->cookieParams = new Params($cookieParams);
        $this->uploadedFiles = new UploadedFiles($uploadedFiles);
        $this->attributes = new Params($attributes);
        $this->setProtocolVersion($protocolVersion);
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
        return $this->parsedBody;
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
        $cloned = clone $this;
        $cloned->cookieParams = new Params($cookies);
        return $cloned;
    }
    
    public function withParsedBody($data): ServerRequestInterface
    {
        if (is_array($data) || is_object($data)) {
            $cloned = clone $this;
            $cloned->parsedBody = $cloned;
            return $cloned;
        }
        
        return $this;
    }
    
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $cloned = clone $this;
        $cloned->queryParams = new QueryParams($query);
        return $cloned;
    }
    
    public function withUploadedFiles(
        array $uploadedFiles,
    ): ServerRequestInterface {
        $cloned = clone $this;
        $cloned->uploadedFiles = new UploadedFiles($uploadedFiles);
        return $cloned;
    }
    
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        if (!$this->attributes->has($name)) {
            return $this;
        }
        
        $cloned = clone $this;
        $cloned->attributes->remove($name);
        return $cloned;
    }
}
