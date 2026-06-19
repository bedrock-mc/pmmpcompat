<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class TimingsCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("timings", "timings command", "/timings");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
