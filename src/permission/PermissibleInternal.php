<?php

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\utils\ObjectSet;
use pocketmine\utils\Utils;

class PermissibleInternal implements Permissible
{
    /** @var array<string, bool> */
    private array $rootPermissions;
    /** @var array<int, PermissionAttachment> */
    private array $attachments = [];
    /** @var array<string, PermissionAttachmentInfo> */
    private array $permissions = [];
    private ObjectSet $permissionRecalculationCallbacks;

    /** @param array<string, bool> $basePermissions */
    public function __construct(array $basePermissions)
    {
        $this->permissionRecalculationCallbacks = new ObjectSet();
        $this->rootPermissions = [];
        foreach (Utils::stringifyKeys($basePermissions) as $name => $value) {
            $this->rootPermissions[$name] = (bool) $value;
        }
        $this->recalculatePermissions();
    }

    public function setBasePermission(Permission|string $name, bool $grant): void
    {
        $this->rootPermissions[$name instanceof Permission ? $name->getName() : $name] = $grant;
        $this->recalculatePermissions();
    }

    public function unsetBasePermission(Permission|string $name): void
    {
        unset($this->rootPermissions[$name instanceof Permission ? $name->getName() : $name]);
        $this->recalculatePermissions();
    }

    public function isPermissionSet(Permission|string $name): bool
    {
        return isset($this->permissions[$name instanceof Permission ? $name->getName() : $name]);
    }

    public function hasPermission(Permission|string $name): bool
    {
        $name = $name instanceof Permission ? $name->getName() : $name;
        return ($this->permissions[$name] ?? null)?->getValue() ?? false;
    }

    public function addAttachment(object $plugin, ?string $name = null, ?bool $value = null): PermissionAttachment
    {
        $attachment = new PermissionAttachment($plugin);
        $this->attachments[spl_object_id($attachment)] = $attachment;
        $attachment->subscribePermissible($this);
        if ($name !== null && $value !== null) {
            $attachment->setPermission($name, $value);
        }
        $this->recalculatePermissions();
        return $attachment;
    }

    public function removeAttachment(PermissionAttachment $attachment): void
    {
        $id = spl_object_id($attachment);
        if (isset($this->attachments[$id])) {
            unset($this->attachments[$id]);
            $attachment->unsubscribePermissible($this);
            $this->recalculatePermissions();
        }
    }

    /** @return array<string, bool> */
    public function recalculatePermissions(): array
    {
        $manager = PermissionManager::getInstance();
        $manager->unsubscribeFromAllPermissions($this);
        $old = $this->permissions;
        $this->permissions = [];
        foreach (Utils::stringifyKeys($this->rootPermissions) as $name => $granted) {
            $info = new PermissionAttachmentInfo($name, null, (bool) $granted, null);
            $this->permissions[$name] = $info;
            $manager->subscribeToPermission($name, $this);
            $permission = $manager->getPermission($name);
            if ($permission !== null) {
                $this->calculateChildPermissions($permission->getChildren(), !$granted, null, $info);
            }
        }
        foreach ($this->attachments as $attachment) {
            $this->calculateChildPermissions($attachment->getPermissions(), false, $attachment, null);
        }
        $diff = [];
        foreach ($this->permissions as $name => $info) {
            if (!isset($old[$name]) || $old[$name]->getValue() !== $info->getValue()) {
                $diff[$name] = ($old[$name] ?? null)?->getValue() ?? false;
            }
            unset($old[$name]);
        }
        foreach ($old as $name => $info) {
            $diff[$name] = $info->getValue();
        }
        if ($diff !== []) {
            foreach ($this->permissionRecalculationCallbacks as $callback) {
                $callback($diff);
            }
        }
        return $diff;
    }

    /** @param array<string, bool> $children */
    private function calculateChildPermissions(array $children, bool $invert, ?PermissionAttachment $attachment, ?PermissionAttachmentInfo $parent): void
    {
        $manager = PermissionManager::getInstance();
        foreach (Utils::stringifyKeys($children) as $name => $value) {
            $granted = ((bool) $value) xor $invert;
            $info = new PermissionAttachmentInfo($name, $attachment, $granted, $parent);
            $this->permissions[$name] = $info;
            $manager->subscribeToPermission($name, $this);
            $permission = $manager->getPermission($name);
            if ($permission !== null) {
                $this->calculateChildPermissions($permission->getChildren(), !$granted, $attachment, $info);
            }
        }
    }

    public function getPermissionRecalculationCallbacks(): ObjectSet
    {
        return $this->permissionRecalculationCallbacks;
    }

    /** @return array<string, PermissionAttachmentInfo> */
    public function getEffectivePermissions(): array
    {
        return $this->permissions;
    }

    public function destroyCycles(): void
    {
        PermissionManager::getInstance()->unsubscribeFromAllPermissions($this);
        foreach ($this->attachments as $attachment) {
            $attachment->unsubscribePermissible($this);
        }
        $this->attachments = [];
        $this->permissions = [];
        $this->permissionRecalculationCallbacks->clear();
    }
}
