<?php

declare(strict_types=1);

namespace pocketmine\utils;

class StringToTParser
{
    /** @var array<string, \Closure> */
    private array $callbackMap = [];

    public function register(string $alias, \Closure $callback): void
    {
        $key = $this->reprocess($alias);
        if (isset($this->callbackMap[$key])) {
            throw new \InvalidArgumentException('Alias "' . $key . '" is already registered');
        }
        $this->callbackMap[$key] = $callback;
    }

    public function override(string $alias, \Closure $callback): void
    {
        $this->callbackMap[$this->reprocess($alias)] = $callback;
    }

    public function registerAlias(string $existing, string $alias): void
    {
        $existingKey = $this->reprocess($existing);
        if (!isset($this->callbackMap[$existingKey])) {
            throw new \InvalidArgumentException('Cannot register new alias for unknown existing alias "' . $existing . '"');
        }
        $newKey = $this->reprocess($alias);
        if (isset($this->callbackMap[$newKey])) {
            throw new \InvalidArgumentException('Alias "' . $newKey . '" is already registered');
        }
        $this->callbackMap[$newKey] = $this->callbackMap[$existingKey];
    }

    public function parse(string $input): mixed
    {
        $key = $this->reprocess($input);
        return isset($this->callbackMap[$key]) ? ($this->callbackMap[$key])($input) : null;
    }

    protected function reprocess(string $input): string
    {
        return strtolower(str_replace([' ', 'minecraft:'], ['_', ''], trim($input)));
    }

    public function getKnownAliases(): array
    {
        return array_keys($this->callbackMap);
    }
}
