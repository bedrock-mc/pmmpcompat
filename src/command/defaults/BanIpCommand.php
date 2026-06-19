<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

class BanIpCommand extends VanillaCommand
{
    public function __construct()
    {
        parent::__construct("ban-ip", "ban ip command", "/ban-ip");
    }

    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args): bool
    {
        return parent::execute($sender, $commandLabel, $args);
    }
}
