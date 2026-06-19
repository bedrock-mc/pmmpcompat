<?php

declare(strict_types=1);

namespace pocketmine\permission;

class PermissionParser
{
    public const DEFAULT_OP = 'op';
    public const DEFAULT_NOT_OP = 'notop';
    public const DEFAULT_TRUE = 'true';
    public const DEFAULT_FALSE = 'false';

    public const DEFAULT_STRING_MAP = [
        'op' => self::DEFAULT_OP,
        'isop' => self::DEFAULT_OP,
        'operator' => self::DEFAULT_OP,
        'isoperator' => self::DEFAULT_OP,
        'admin' => self::DEFAULT_OP,
        'isadmin' => self::DEFAULT_OP,
        '!op' => self::DEFAULT_NOT_OP,
        'notop' => self::DEFAULT_NOT_OP,
        '!operator' => self::DEFAULT_NOT_OP,
        'notoperator' => self::DEFAULT_NOT_OP,
        '!admin' => self::DEFAULT_NOT_OP,
        'notadmin' => self::DEFAULT_NOT_OP,
        'true' => self::DEFAULT_TRUE,
        'false' => self::DEFAULT_FALSE,
    ];

    public static function defaultFromString(bool|string $value): string
    {
        if (is_bool($value)) {
            return $value ? self::DEFAULT_TRUE : self::DEFAULT_FALSE;
        }
        $lower = strtolower($value);
        if (isset(self::DEFAULT_STRING_MAP[$lower])) {
            return self::DEFAULT_STRING_MAP[$lower];
        }
        throw new PermissionParserException('Unknown permission default name "' . $value . '"');
    }

    public static function loadPermissions(array $data, string $default = self::DEFAULT_FALSE): array
    {
        $result = [];
        foreach ($data as $name => $entry) {
            if (!is_array($entry)) {
                throw new PermissionParserException('Permission "' . (string) $name . '" must be an array');
            }
            $permissionDefault = $default;
            if (array_key_exists('default', $entry)) {
                $permissionDefault = self::defaultFromString($entry['default']);
            }
            if (array_key_exists('children', $entry)) {
                throw new PermissionParserException('Nested permission declarations are no longer supported. Declare each permission separately.');
            }
            $description = (string) ($entry['description'] ?? '');
            $result[$permissionDefault][] = new Permission((string) $name, $description);
        }
        return $result;
    }
}
