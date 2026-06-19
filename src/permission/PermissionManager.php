<?php

declare(strict_types=1);

namespace pocketmine\permission;

class PermissionManager
{
    private static ?self $instance = null;

    /** @var array<string, Permission> */
    private array $permissions = [];
    /** @var array<string, array<int, object>> */
    private array $permissionSubscriptions = [];

    public function __construct()
    {
        self::$instance ??= $this;
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function addPermission(Permission $permission): bool
    {
        $key = strtolower($permission->getName());
        if (isset($this->permissions[$key])) {
            return false;
        }
        $this->permissions[$key] = $permission;
        return true;
    }

    public function getPermission(string $name): ?Permission
    {
        return $this->permissions[strtolower($name)] ?? null;
    }

    public function removePermission(Permission|string $permission): void
    {
        $name = $permission instanceof Permission ? $permission->getName() : $permission;
        unset($this->permissions[strtolower($name)]);
    }

    public function clearPermissions(): void
    {
        $this->permissions = [];
    }

    public function subscribeToPermission(string $permission, object $permissible): void
    {
        $key = strtolower($permission);
        $this->permissionSubscriptions[$key][spl_object_id($permissible)] = $permissible;
    }

    public function unsubscribeFromPermission(string $permission, object $permissible): void
    {
        $key = strtolower($permission);
        unset($this->permissionSubscriptions[$key][spl_object_id($permissible)]);
        if (($this->permissionSubscriptions[$key] ?? []) === []) {
            unset($this->permissionSubscriptions[$key]);
        }
    }

    public function unsubscribeFromAllPermissions(object $permissible): void
    {
        $id = spl_object_id($permissible);
        foreach ($this->permissionSubscriptions as $permission => $subscribers) {
            unset($subscribers[$id]);
            if ($subscribers === []) {
                unset($this->permissionSubscriptions[$permission]);
            } else {
                $this->permissionSubscriptions[$permission] = $subscribers;
            }
        }
    }

    /** @return object[] */
    public function getPermissionSubscriptions(string $permission): array
    {
        return array_values($this->permissionSubscriptions[strtolower($permission)] ?? []);
    }

    /** @return Permission[] */
    public function getPermissions(): array
    {
        return array_values($this->permissions);
    }
}
