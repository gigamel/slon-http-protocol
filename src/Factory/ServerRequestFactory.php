<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Factory;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slon\Http\Protocol\Enum\Method;
use Slon\Http\Protocol\ServerRequest;
use Slon\Http\Protocol\Uri;
use Slon\Streams\Stream;

use function array_map;
use function is_string;

use function Slon\Http\Protocol\normalize_server_files;
use function Slon\Http\Protocol\parse_server_headers;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function __construct(
        protected array $queryParams = [],
        protected array $formParams = [],
        protected array $serverParams = [],
        protected array $cookies = [],
        protected array $files = [],
    ) {}
    
    public static function fromGlobals(): ServerRequestInterface
    {
        return (new self(
            $_GET,
            $_POST,
            $_SERVER,
            $_COOKIE,
            $_FILES,
        ))->createServerRequest(
            $_SERVER['REQUEST_METHOD'] ?? Method::GET,
            $_SERVER['REQUEST_URI'] ?? '',
        );
    }
    
    public function createServerRequest(
        string $method,
        $uri,
        array $serverParams = [],
    ): ServerRequestInterface {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }
        
        if (!$uri instanceof UriInterface) {
            throw new InvalidArgumentException(
                'Uri should be type of string|UriInterface',
            );
        }
        
        $headers = parse_server_headers($serverParams ?: $this->serverParams);
        $uploadFileFactory = new UploadedFileFactory();
        
        return new ServerRequest(
            $method,
            $uri,
            $headers,
            $this->queryParams,
            $this->formParams,
            $serverParams ?: $this->serverParams,
            $this->cookies ?: ($headers['cookie'] ?? []),
            array_map(
                static function (array $file) use ($uploadFileFactory) {
                    return $uploadFileFactory->createUploadedFile(
                        new Stream($file['tmp_name'], 'r'),
                        $file['size'],
                        $file['error'],
                        $file['name'],
                        $file['type'],
                    );
                },
                normalize_server_files($this->files),
            ),
        );
    }
}
