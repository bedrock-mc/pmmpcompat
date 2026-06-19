<?php

declare(strict_types=1);

namespace pocketmine\permission;

class Permission
{
    public const DEFAULT_FALSE = 'false';
    public const DEFAULT_TRUE = 'true';
    public const DEFAULT_OP = 'op';
    public const DEFAULT_NOT_OP = 'notop';

    private string $default;

    /** @param array<string, bool> $children */
    public function __construct(
        private string $name,
        private string $description = '',
        string|array $default = self::DEFAULT_FALSE,
        private array $children = [],
    ) {
        if (is_array($default)) {
            $this->children = $default;
            $this->default = self::DEFAULT_FALSE;
        } else {
            $this->default = $default;
        }
    }

    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $value): void { $this->description = $value; }
    public function getDefault(): string { return $this->default; }
    /** @return array<string, bool> */
    public function getChildren(): array { return $this->children; }

    /** @return object[] */
    public function getPermissibles(): array
    {
        return PermissionManager::getInstance()->getPermissionSubscriptions($this->name);
    }

    public function recalculatePermissibles(): void
    {
        foreach ($this->getPermissibles() as $permissible) {
            if (method_exists($permissible, 'recalculatePermissions')) {
                $permissible->recalculatePermissions();
            }
        }
    }

    public function addChild(string $name, bool $value): void
    {
        $this->children[$name] = $value;
        $this->recalculatePermissibles();
    }

    public function removeChild(string $name): void
    {
        unset($this->children[$name]);
        $this->recalculatePermissibles();
    }
}
