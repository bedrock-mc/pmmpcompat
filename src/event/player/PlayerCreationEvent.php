<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Event;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class PlayerCreationEvent extends Event
{
    private string $baseClass = Player::class;
    private string $playerClass = Player::class;

    public function __construct(private NetworkSession $session) {}

    public function getNetworkSession(): NetworkSession { return $this->session; }
    public function getAddress(): string { return (string) $this->session->getIp(); }
    public function getPort(): int { return (int) $this->session->getPort(); }
    public function getBaseClass(): string { return $this->baseClass; }
    public function getPlayerClass(): string { return $this->playerClass; }

    public function setBaseClass(string $class): void
    {
        if (!is_a($class, $this->baseClass, true)) {
            throw new \RuntimeException('Base class ' . $class . ' must extend ' . $this->baseClass);
        }
        $this->baseClass = $class;
    }

    public function setPlayerClass(string $class): void
    {
        Utils::testValidInstance($class, $this->baseClass);
        $this->playerClass = $class;
    }
}
