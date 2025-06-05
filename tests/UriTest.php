<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slon\Http\Protocol\Uri;

class UriTest extends TestCase
{
    public static function constructorDataProvider(): array
    {
        return [
            [
                'uri' => 'https://user:pass@localhost:8000/?var=value#test',
                'scheme' => 'https://',
                'host' => 'localhost',
                'port' => 8000,
                'userInfo' => 'user:pass',
                'authority' => 'user:pass@localhost:8000',
                'path' => '/',
                'query' => '?var=value',
                'fragment' => '#test',
            ],
            [
                'uri' => 'localhost:8000/test/?var=value#test',
                'scheme' => '',
                'host' => 'localhost',
                'port' => 8000,
                'userInfo' => '',
                'authority' => 'localhost:8000',
                'path' => '/test/',
                'query' => '?var=value',
                'fragment' => '#test',
            ],
            [
                'uri' => 'localhost/?var=value',
                'scheme' => '',
                'host' => '',
                'port' => null,
                'userInfo' => '',
                'authority' => '',
                'path' => 'localhost/',
                'query' => '?var=value',
                'fragment' => '',
            ],
            [
                'uri' => 'localhost.test/?var=value',
                'scheme' => '',
                'host' => '',
                'port' => null,
                'userInfo' => '',
                'authority' => '',
                'path' => 'localhost.test/',
                'query' => '?var=value',
                'fragment' => '',
            ],
        ];
    }
    
    #[DataProvider('constructorDataProvider')]
    public function testGetters(
        string $uri,
        string $scheme,
        string $host,
        ?int $port,
        string $userInfo,
        string $authority,
        string $path,
        string $query,
        string $fragment,
    ): void {
        $uri = new Uri($uri);
        
        $this->assertEquals($uri->getScheme(), $scheme);
        $this->assertEquals($uri->getHost(), $host);
        $this->assertEquals($uri->getPort(), $port);
        $this->assertEquals($uri->getUserInfo(), $userInfo);
        $this->assertEquals($uri->getAuthority(), $authority);
        $this->assertEquals($uri->getPath(), $path);
        $this->assertEquals($uri->getQuery(), $query);
        $this->assertEquals($uri->getFragment(), $fragment);
    }
}
