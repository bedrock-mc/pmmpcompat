<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class DifficultyCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("difficulty", "difficulty command", "/difficulty");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
