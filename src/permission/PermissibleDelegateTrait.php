<?php

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\utils\ObjectSet;

trait PermissibleDelegateTrait
{
    private Permissible $perm;

    public function setBasePermission(Permission|string $name, bool $grant): void { $this->perm->setBasePermission($name, $grant); }
    public function unsetBasePermission(Permission|string $name): void { $this->perm->unsetBasePermission($name); }
    public function isPermissionSet(Permission|string $name): bool { return $this->perm->isPermissionSet($name); }
    public function hasPermission(Permission|string $name): bool { return $this->perm->hasPermission($name); }
    public function addAttachment(object $plugin, ?string $name = null, ?bool $value = null): PermissionAttachment { return $this->perm->addAttachment($plugin, $name, $value); }
    public function removeAttachment(PermissionAttachment $attachment): void { $this->perm->removeAttachment($attachment); }
    public function recalculatePermissions(): array { return $this->perm->recalculatePermissions(); }
    public function getPermissionRecalculationCallbacks(): ObjectSet { return $this->perm->getPermissionRecalculationCallbacks(); }
    public function getEffectivePermissions(): array { return $this->perm->getEffectivePermissions(); }
}
