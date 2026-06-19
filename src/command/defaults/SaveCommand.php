<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class SaveCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("save-all", "save command", "/save-all");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
