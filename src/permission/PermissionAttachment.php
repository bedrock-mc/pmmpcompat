<?php

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\utils\Utils;

class PermissionAttachment
{
    /** @var array<string, bool> */
    private array $permissions = [];
    /** @var array<int, PermissibleInternal> */
    private array $subscribers = [];

    public function __construct(private object $plugin)
    {
        if (method_exists($plugin, 'isEnabled') && !$plugin->isEnabled()) {
            throw new \RuntimeException('Plugin ' . (method_exists($plugin, 'getName') ? $plugin->getName() : get_debug_type($plugin)) . ' is disabled');
        }
    }

    public function getPlugin(): object { return $this->plugin; }
    /** @return array<int, PermissibleInternal> */
    public function getSubscribers(): array { return $this->subscribers; }
    /** @return array<string, bool> */
    public function getPermissions(): array { return $this->permissions; }

    private function recalculatePermissibles(): void
    {
        foreach ($this->subscribers as $permissible) {
            $permissible->recalculatePermissions();
        }
    }

    public function clearPermissions(): void
    {
        $this->permissions = [];
        $this->recalculatePermissibles();
    }

    /** @param array<string, bool> $permissions */
    public function setPermissions(array $permissions): void
    {
        foreach (Utils::stringifyKeys($permissions) as $key => $value) {
            $this->permissions[$key] = (bool) $value;
        }
        $this->recalculatePermissibles();
    }

    /** @param string[] $permissions */
    public function unsetPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            unset($this->permissions[$permission]);
        }
        $this->recalculatePermissibles();
    }

    public function setPermission(Permission|string $name, bool $value): void
    {
        $name = $name instanceof Permission ? $name->getName() : $name;
        if (($this->permissions[$name] ?? null) === $value) {
            return;
        }
        unset($this->permissions[$name]);
        $this->permissions[$name] = $value;
        $this->recalculatePermissibles();
    }

    public function unsetPermission(Permission|string $name): void
    {
        $name = $name instanceof Permission ? $name->getName() : $name;
        if (array_key_exists($name, $this->permissions)) {
            unset($this->permissions[$name]);
            $this->recalculatePermissibles();
        }
    }

    public function subscribePermissible(PermissibleInternal $permissible): void
    {
        $this->subscribers[spl_object_id($permissible)] = $permissible;
    }

    public function unsubscribePermissible(PermissibleInternal $permissible): void
    {
        unset($this->subscribers[spl_object_id($permissible)]);
    }
}
