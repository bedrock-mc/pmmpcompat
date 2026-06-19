<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\Server;

class SimpleCommandMap implements CommandMap
{
    /** @var array<string, Command> */
    private array $commands = [];

    public function __construct(private ?Server $server = null) {}

    /** @param Command[] $commands */
    public function registerAll(string $fallbackPrefix, array $commands): void
    {
        foreach ($commands as $command) {
            $this->register($fallbackPrefix, $command);
        }
    }

    public function register(string $fallbackPrefix, Command $command, ?string $label = null): bool
    {
        $label ??= $command->getName();
        $label = strtolower(trim($label));
        $registered = !isset($this->commands[$label]);
        $this->commands[$label] = $command;
        foreach ($command->getAliases() as $alias) {
            $this->commands[strtolower($alias)] = $command;
        }
        $command->register($this);
        return $registered;
    }

    public function unregister(Command $command): bool
    {
        foreach ($this->commands as $label => $registered) {
            if ($registered === $command) {
                unset($this->commands[$label]);
            }
        }
        $command->unregister($this);
        return true;
    }

    public function clearCommands(): void
    {
        foreach (array_unique($this->commands, SORT_REGULAR) as $command) {
            $command->unregister($this);
        }
        $this->commands = [];
    }

    /** @return array<string, Command> */
    public function getCommands(): array
    {
        return $this->commands;
    }

    public function registerServerAliases(): void {}

    public function getCommand(string $name): ?Command
    {
        return $this->commands[strtolower($name)] ?? null;
    }

    /** @param string[] $args */
    public function dispatch(CommandSender $sender, string $name, array $args): bool
    {
        $command = $this->getCommand($name);
        return $command !== null && $command->execute($sender, $name, $args);
    }
}
