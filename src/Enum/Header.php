<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Enum;

final readonly class Header
{
    public const string AUTHORIZATION = 'Authorization';
    
    public const string CACHE_CONTROL = 'Cache-Control';
    
    public const string CONTENT_LENGTH = 'Content-Length';
    
    public const string CONTENT_TYPE = 'Content-Type';
    
    public const string HOST = 'Host';
    
    public const string LOCATION = 'Location';
    
    public const string REFERER = 'Referer';
}
