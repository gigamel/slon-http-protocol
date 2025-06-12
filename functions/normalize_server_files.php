<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use AssertionError;

use function array_keys;
use function is_array;

/**
 * @throws AssertionError
 */
function normalize_server_Files(array $files): array
{
    $parsedFiles = [];
    foreach ($files as $file) {
        assert_server_file($file);
        
        if (0 === ($file['size'][0] ?? $file['size'])) {
            continue;
        }
        
        if (!is_array($file['name'])) {
            $parsedFiles[] = $file;
            
            continue;
        }
        
        foreach (array_keys($file['name']) as $key) {
            $parsedFiles[] = [
                'tmp_name' => $file['tmp_name'][$key],
                'error' => $file['error'][$key],
                'size' => $file['size'][$key],
                'name' => $file['name'][$key],
                'type' => $file['type'][$key],
            ];
        }
    }
    
    return $parsedFiles;
}
