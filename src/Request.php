<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\Enum\Version;
use Slon\Http\Protocol\Message\Headers;
use Slon\Streams\MemoryStream;

use function is_string;

class Request implements RequestInterface
{
    use MessageTrait;
    use RequestTrait;
    
    public function __construct(
        string $method,
        string|UriInterface $uri,
        array $headers = [],
        ?string $body = null,
        string $protocolVersion = Version::HTTP_1_1,
    ) {
        $this->setMethod($method);
        
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }
        
        $this->uri = $uri;
        $this->headers = new Headers($headers);
        
        $this->body = new MemoryStream();
        if ($body) {
            $this->getBody()->write($body);
        }
        
        $this->setProtocolVersion($protocolVersion);
    }
}
