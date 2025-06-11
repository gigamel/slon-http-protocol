<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Factory;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\Request;
use Slon\Http\Protocol\Uri;

use function is_string;

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }
        
        if (!$uri instanceof UriInterface) {
            throw new InvalidArgumentException(
                'Uri should type of string|UriInterface',
            );
        }
        
        return new Request($method, $uri);
    }
}
