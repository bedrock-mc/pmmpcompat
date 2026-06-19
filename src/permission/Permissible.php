<?php

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\utils\ObjectSet;

interface Permissible
{
    public function setBasePermission(Permission|string $name, bool $grant): void;
    public function unsetBasePermission(Permission|string $name): void;
    public function isPermissionSet(Permission|string $name): bool;
    public function hasPermission(Permission|string $name): bool;
    public function addAttachment(object $plugin, ?string $name = null, ?bool $value = null): PermissionAttachment;
    public function removeAttachment(PermissionAttachment $attachment): void;
    public function recalculatePermissions(): array;
    public function getPermissionRecalculationCallbacks(): ObjectSet;
    public function getEffectivePermissions(): array;
}
