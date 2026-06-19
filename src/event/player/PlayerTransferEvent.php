<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class PlayerTransferEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Player $player,
        protected string $address,
        protected int $port,
        protected Translatable|string $message
    ) {
        parent::__construct($player);
    }

    public function getAddress(): string { return $this->address; }
    public function setAddress(string $address): void { $this->address = $address; }
    public function getPort(): int { return $this->port; }
    public function setPort(int $port): void { $this->port = $port; }
    public function getMessage(): Translatable|string { return $this->message; }
    public function setMessage(Translatable|string $message): void { $this->message = $message; }
}
