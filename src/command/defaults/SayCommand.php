<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class SayCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("say", "say command", "/say");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
