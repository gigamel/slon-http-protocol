<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\ServerMessage;

final class ServerParams
{
    private array $params = [];
    
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
    
    protected function add(string $name, mixed $value): void
    {
        $this->params[$name] = $value;
    }
}
