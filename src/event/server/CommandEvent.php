<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\command\CommandSender;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class CommandEvent extends ServerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(private CommandSender $sender, private string $command) {}

    public function getSender(): CommandSender { return $this->sender; }
    public function getCommand(): string { return $this->command; }
    public function setCommand(string $command): void { $this->command = $command; }
}
