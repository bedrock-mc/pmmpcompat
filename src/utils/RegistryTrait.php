<?php

declare(strict_types=1);

namespace pocketmine\utils;

trait RegistryTrait
{
    /** @var array<string, object>|null */
    private static ?array $members = null;

    private static function verifyName(string $name): void
    {
        if (preg_match('/^(?!\d)[A-Za-z\d_]+$/u', $name) !== 1) {
            throw new \InvalidArgumentException('Invalid member name "' . $name . '"');
        }
    }

    private static function _registryRegister(string $name, object $member): void
    {
        if (self::$members === null) {
            throw new AssumptionFailedError('Cannot register members outside of ' . self::class . '::setup()');
        }
        self::verifyName($name);
        $upper = strtoupper($name);
        if (isset(self::$members[$upper])) {
            throw new \InvalidArgumentException('"' . $upper . '" is already reserved');
        }
        self::$members[$upper] = $member;
    }

    abstract protected static function setup(): void;

    protected static function checkInit(): void
    {
        if (self::$members === null) {
            self::$members = [];
            self::setup();
        }
    }

    private static function _registryFromString(string $name): object
    {
        self::checkInit();
        $upper = strtoupper($name);
        if (!isset(self::$members[$upper])) {
            throw new \InvalidArgumentException('No such registry member: ' . self::class . '::' . $upper);
        }
        return self::preprocessMember(self::$members[$upper]);
    }

    protected static function preprocessMember(object $member): object
    {
        return $member;
    }

    public static function __callStatic($name, $arguments)
    {
        if (count($arguments) > 0) {
            throw new \ArgumentCountError('Expected exactly 0 arguments, ' . count($arguments) . ' passed');
        }
        if (self::$members !== null && isset(self::$members[$name])) {
            return self::preprocessMember(self::$members[$name]);
        }
        try {
            return self::_registryFromString($name);
        } catch (\InvalidArgumentException $e) {
            throw new \Error($e->getMessage(), 0, $e);
        }
    }

    private static function _registryGetAll(): array
    {
        self::checkInit();
        return array_map(self::preprocessMember(...), self::$members ?? []);
    }
}
