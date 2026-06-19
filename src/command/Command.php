<?php

declare(strict_types=1);

namespace pocketmine\command;

class Command
{
    /** @param string[] $aliases */
    public function __construct(
        private string $name,
        private string $description = '',
        private array $aliases = [],
        private ?string $permission = null,
        private string $permissionMessage = '',
        private string $usage = '',
        private string $label = '',
    ) {
        $this->label = $this->label === '' ? $this->name : $this->label;
        $this->usage = $this->usage === '' ? '/' . $this->name : $this->usage;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /** @param string[] $aliases */
    public function setAliases(array $aliases): void
    {
        $this->aliases = array_values(array_map('strval', $aliases));
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(?string $permission): void
    {
        $this->permission = $permission;
    }

    /** @return string[] */
    public function getPermissions(): array
    {
        if ($this->permission === null || $this->permission === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(';', $this->permission)), static fn(string $permission): bool => $permission !== ''));
    }

    /** @param string[] $permissions */
    public function setPermissions(array $permissions): void
    {
        $this->permission = implode(';', array_map('strval', $permissions));
    }

    public function getPermissionMessage(): string
    {
        return $this->permissionMessage;
    }

    public function setPermissionMessage(string $message): void
    {
        $this->permissionMessage = $message;
    }

    public function testPermission(CommandSender $sender): bool
    {
        if ($this->testPermissionSilent($sender)) {
            return true;
        }
        if ($this->permissionMessage !== '') {
            $sender->sendMessage($this->permissionMessage);
        }
        return false;
    }

    public function testPermissionSilent(CommandSender $sender): bool
    {
        if ($this->permission === null || $this->permission === '') {
            return true;
        }
        foreach (explode(';', $this->permission) as $permission) {
            if ($sender->hasPermission(trim($permission))) {
                return true;
            }
        }
        return false;
    }

    public function getUsage(): string
    {
        return $this->usage;
    }

    public function setUsage(string $usage): void
    {
        $this->usage = $usage;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): bool
    {
        $this->label = $label;
        return true;
    }

    public function isRegistered(): bool
    {
        return true;
    }

    public function register(CommandMap $commandMap): bool
    {
        return true;
    }

    public function unregister(CommandMap $commandMap): bool
    {
        return true;
    }

    /** @param string[] $args */
    public function execute(CommandSender $sender, string $label, array $args): bool
    {
        return false;
    }

    public static function broadcastCommandMessage(CommandSender $source, string $message, bool $sendToSource = true): void
    {
        if ($sendToSource) {
            $source->sendMessage($message);
        }
    }
}
