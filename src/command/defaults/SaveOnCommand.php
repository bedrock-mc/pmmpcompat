<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class SaveOnCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("save-on", "save on command", "/save-on");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
