<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class SaveOffCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("save-off", "save off command", "/save-off");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
