<?php

declare(strict_types=1);

namespace pocketmine\permission;

final class PermissibleBase implements Permissible
{
    use PermissibleDelegateTrait;

    private PermissibleInternal $permissibleBase;

    /** @param array<string, bool> $basePermissions */
    public function __construct(array $basePermissions = [])
    {
        $this->permissibleBase = new PermissibleInternal($basePermissions);
        $this->perm = $this->permissibleBase;
    }

    public function __destruct()
    {
        $this->permissibleBase->destroyCycles();
    }
}
