<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class SpawnpointCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("spawnpoint", "spawnpoint command", "/spawnpoint");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
