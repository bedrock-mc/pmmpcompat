<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class SetWorldSpawnCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("setworldspawn", "set world spawn command", "/setworldspawn");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
