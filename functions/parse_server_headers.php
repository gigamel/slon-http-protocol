<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use function is_string;
use function strtolower;
use function str_starts_with;
use function substr;

/**
 * @param array<string, string> $server
 *
 * @return array<string, array>
 */
function parse_server_headers(array $server): array
{
    $headers = [];
    foreach ($server as $key => $value) {
        if (!is_string($key) || !is_string($value)) {
            continue;
        }
        
        if (!str_starts_with($key, 'HTTP_')) {
            continue;
        }
        
        $headers[strtolower(substr($key, 5))] = explode(';', $value);
    }
    
    return $headers;
}
