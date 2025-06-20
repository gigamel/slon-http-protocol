<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Message;

use function is_array;

final class QueryParams extends Params
{
    /**
     * @inheritDoc
     */
    protected function normalizeValue(string $name, mixed $value): mixed
    {
        if (is_array($value)) {
            return $value;
        }
        
        return parent::normalizeValue($name, $value);
    }
}
