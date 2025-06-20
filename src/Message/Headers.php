<?php

declare(strict_types=1);

namespace Slon\Http\Protocol\Message;

use InvalidArgumentException;

use function array_key_exists;
use function implode;
use function in_array;
use function is_string;
use function sprintf;
use function strtolower;
use function str_replace;
use function ucwords;

final class Headers
{
    protected array $headers = [];

    public function __construct(array $headers = [])
    {
        foreach ($headers as $name => $value) {
            $this->add($name, $value);
        }
    }
    
    public function add(string $name, string|array $value): void
    {
        if (is_string($value)) {
            $value = [$value];
        }
        
        foreach ($value as $chunk) {
            $this->addHeaderChunk($this->nameToLowerCase($name), $chunk);
        }
    }
    
    public function all(): array
    {
        return $this->headersToProtocol($this->headers);
    }
    
    public function get(string $name): array
    {
        return $this->headers[$this->nameToLowerCase($name)] ?? [];
    }
    
    public function getLine(string $name): string
    {
        if ($this->has($name)) {
            return implode(';', $this->get($name));
        }
        
        return '';
    }

    public function has(string $name): bool
    {
        return array_key_exists(
            $this->nameToLowerCase($name),
            $this->headers,
        );
    }
    
    public function remove(string $name): void
    {
        unset($this->headers[$this->nameToLowerCase($name)]);
    }
    
    public function set(string $name, string|array $value): void
    {
        $this->headers[$this->nameToLowerCase($name)] = [];
        $this->add($name, $value);
    }
    
    private function nameToLowerCase(string $name): string
    {
        return strtolower(str_replace('_', '-', $name));
    }
    
    private function nameToUpperCase(string $name): string
    {
        return ucwords($name, '-');
    }
    
    private function headersToProtocol(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $value) {
            $normalized[$this->nameToUpperCase($name)] = $value;
        }
        
        return $normalized;
    }
    
    private function addHeaderChunk(string $name, string $chunk): void
    {
        if (empty($chunk)) {
            throw new InvalidArgumentException(sprintf(
                'Header "%s" chunk is empty',
                $name,
            ));
        }
        
        if (in_array($chunk, $this->headers[$name] ?? [], true)) {
            return;
        }
        
        $this->headers[$name][] = $chunk;
    }
}
