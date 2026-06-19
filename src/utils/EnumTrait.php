<?php

declare(strict_types=1);

namespace pocketmine\utils;

trait EnumTrait
{
    use RegistryTrait;

    private static ?int $nextId = null;
    private string $enumName;
    private int $runtimeId;

    protected static function register(self $member): void
    {
        self::_registryRegister($member->name(), $member);
    }

    protected static function registerAll(self ...$members): void
    {
        foreach ($members as $member) {
            self::register($member);
        }
    }

    public static function getAll(): array
    {
        return self::_registryGetAll();
    }

    private function __construct(string $enumName)
    {
        self::verifyName($enumName);
        $this->enumName = $enumName;
        self::$nextId ??= Process::pid();
        $this->runtimeId = self::$nextId++;
    }

    public function name(): string
    {
        return $this->enumName;
    }

    public function id(): int
    {
        return $this->runtimeId;
    }

    public function equals(self $other): bool
    {
        return $this->enumName === $other->enumName;
    }
}
