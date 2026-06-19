<?php

declare(strict_types=1);

namespace pocketmine;

use pocketmine\utils\Config;

final class ServerConfigGroup
{
    /** @var array<string, mixed> */
    private array $propertyCache = [];

    public function __construct(private Config $pocketmineYml, private Config $serverProperties) {}

    public function getProperty(string $variable, mixed $defaultValue = null): mixed
    {
        if (!array_key_exists($variable, $this->propertyCache)) {
            $this->propertyCache[$variable] = $this->pocketmineYml->getNested($variable);
        }
        return $this->propertyCache[$variable] ?? $defaultValue;
    }

    public function getPropertyBool(string $variable, bool $defaultValue): bool
    {
        return (bool) $this->getProperty($variable, $defaultValue);
    }

    public function getPropertyInt(string $variable, int $defaultValue): int
    {
        return (int) $this->getProperty($variable, $defaultValue);
    }

    public function getPropertyString(string $variable, string $defaultValue): string
    {
        return (string) $this->getProperty($variable, $defaultValue);
    }

    public function getConfigString(string $variable, string $defaultValue = ''): string
    {
        return $this->serverProperties->exists($variable) ? (string) $this->serverProperties->get($variable) : $defaultValue;
    }

    public function setConfigString(string $variable, string $value): void
    {
        $this->serverProperties->set($variable, $value);
    }

    public function getConfigInt(string $variable, int $defaultValue = 0): int
    {
        return $this->serverProperties->exists($variable) ? (int) $this->serverProperties->get($variable) : $defaultValue;
    }

    public function setConfigInt(string $variable, int $value): void
    {
        $this->serverProperties->set($variable, $value);
    }

    public function getConfigBool(string $variable, bool $defaultValue = false): bool
    {
        $value = $this->serverProperties->exists($variable) ? $this->serverProperties->get($variable) : $defaultValue;
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value !== 0;
        }
        if (is_string($value)) {
            return match (strtolower($value)) {
                'on', 'true', '1', 'yes' => true,
                default => false,
            };
        }
        return false;
    }

    public function setConfigBool(string $variable, bool $value): void
    {
        $this->serverProperties->set($variable, $value ? '1' : '0');
    }

    public function save(): void
    {
        if ($this->serverProperties->hasChanged()) {
            $this->serverProperties->save();
        }
        if ($this->pocketmineYml->hasChanged()) {
            $this->pocketmineYml->save();
        }
    }
}
