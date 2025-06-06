<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Message;

use InvalidArgumentException;

use function ctype_digit;
use function is_numeric;
use function is_scalar;
use function sprintf;

class Params
{
    protected array $params = [];

    public function __construct(array $params = [])
    {
        foreach ($params as $name => $value) {
            $this->add($name, $value);
        }
    }

    public function all(): array
    {
        return $this->params;
    }
    
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->params[$name] ?? null;
    }
    
    public function add(string $name, mixed $value): void
    {
        $this->params[$name] = $this->normalizeValue($name, $value);
    }
    
    /**
     * @throws InvalidArgumentException
     */
    protected function normalizeValue(
        string $name,
        mixed $value,
    ): mixed {
        if (!is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                'Query param "%s" must be of scalar',
                $name,
            ));
        }
        
        if (ctype_digit($value)) {
            return (int) $value;
        }
        
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        if ('true' === $value) {
            return true;
        }
        
        if ('false' === $value) {
            return false;
        }
        
        return $value;
    }
}
