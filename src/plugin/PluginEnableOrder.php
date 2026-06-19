<?php

declare(strict_types=1);

namespace pocketmine\plugin;

final class PluginEnableOrder
{
    private function __construct(private string $name) {}

    public static function STARTUP(): self { return new self('startup'); }
    public static function POSTWORLD(): self { return new self('postworld'); }

    public static function fromString(string $name): ?self
    {
        return match (strtolower($name)) {
            'startup' => self::STARTUP(),
            'postworld' => self::POSTWORLD(),
            default => null,
        };
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return [$this->name];
    }

    public function name(): string
    {
        return strtoupper($this->name);
    }
}
