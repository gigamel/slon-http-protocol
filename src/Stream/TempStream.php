<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Stream;

use Slon\Http\Protocol\Stream;

final class TempStream
{
    public function __construct()
    {
        parent::__construct('php://temp', 'w+b');
    }
}
