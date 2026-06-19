<?php

declare(strict_types=1);

namespace pocketmine\permission;

class DefaultPermissions
{
    public const ROOT_CONSOLE = DefaultPermissionNames::GROUP_CONSOLE;
    public const ROOT_OPERATOR = DefaultPermissionNames::GROUP_OPERATOR;
    public const ROOT_USER = DefaultPermissionNames::GROUP_USER;

    public static function registerPermission(Permission $candidate, array $grantedBy = [], array $deniedBy = []): Permission
    {
        foreach ($grantedBy as $permission) {
            $permission->addChild($candidate->getName(), true);
        }
        foreach ($deniedBy as $permission) {
            $permission->addChild($candidate->getName(), false);
        }
        PermissionManager::getInstance()->addPermission($candidate);
        return PermissionManager::getInstance()->getPermission($candidate->getName()) ?? $candidate;
    }

    public static function registerCorePermissions(): void
    {
        $consoleRoot = self::registerPermission(new Permission(self::ROOT_CONSOLE, 'Console commands'));
        $operatorRoot = self::registerPermission(new Permission(self::ROOT_OPERATOR, 'Operator commands'), [$consoleRoot]);
        $userRoot = self::registerPermission(new Permission(self::ROOT_USER, 'User commands'), [$operatorRoot]);
        foreach ((new \ReflectionClass(DefaultPermissionNames::class))->getConstants() as $name => $permissionName) {
            if (!is_string($permissionName) || str_starts_with($name, 'GROUP_')) {
                continue;
            }
            $root = str_contains($permissionName, '.self') || in_array($permissionName, [
                DefaultPermissionNames::BROADCAST_USER,
                DefaultPermissionNames::COMMAND_HELP,
                DefaultPermissionNames::COMMAND_KILL_SELF,
                DefaultPermissionNames::COMMAND_ME,
                DefaultPermissionNames::COMMAND_TELL,
                DefaultPermissionNames::COMMAND_VERSION,
            ], true) ? $userRoot : $operatorRoot;
            self::registerPermission(new Permission($permissionName, $permissionName), [$root]);
        }
    }
}
