<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class PluginsCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("plugins", "plugins command", "/plugins");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
