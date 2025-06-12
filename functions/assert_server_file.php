<?php

declare(strict_types=1);

namespace Slon\Http\Protocol;

use AssertionError;

use function assert;

/**
 * @throws AssertionError
 */
function assert_server_file(array $file): void
{
    assert(
        isset($file['tmp_name'])
        && isset($file['error'])
        && isset($file['size'])
        && isset($file['name'])
        && isset($file['type'])
    );
}
