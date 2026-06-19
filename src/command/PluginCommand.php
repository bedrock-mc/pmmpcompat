<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\plugin\PluginBase;

class PluginCommand extends Command
{
    private CommandExecutor $executor;

    /** @param array<string, mixed> $spec */
    public function __construct(string $name, private PluginBase $plugin, array $spec = [])
    {
        $this->executor = $plugin;
        $aliases = $spec['aliases'] ?? [];
        if (is_string($aliases)) {
            $aliases = [$aliases];
        }
        parent::__construct(
            $name,
            (string) ($spec['description'] ?? ''),
            isset($spec['usage']) ? (string) $spec['usage'] : null,
            is_array($aliases) ? array_map('strval', $aliases) : [],
            isset($spec['permission']) ? (string) $spec['permission'] : null,
            (string) ($spec['permission-message'] ?? $spec['permission_message'] ?? ''),
        );
    }

    /** @param string[] $args */
    public function execute(CommandSender $sender, string $label, array $args): bool
    {
        if (!$this->testPermission($sender)) {
            return true;
        }
        return $this->executor->onCommand($sender, $this, $label, $args);
    }

    public function getOwningPlugin(): PluginBase
    {
        return $this->plugin;
    }

    public function getExecutor(): CommandExecutor
    {
        return $this->executor;
    }

    public function setExecutor(CommandExecutor $executor): void
    {
        $this->executor = $executor;
    }
}
