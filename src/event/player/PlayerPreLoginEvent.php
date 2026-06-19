<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Event;
use pocketmine\lang\Translatable;
use pocketmine\player\PlayerInfo;

class PlayerPreLoginEvent extends \pocketmine\event\Event
{
    public const KICK_FLAG_PLUGIN = 0;
    public const KICK_FLAG_SERVER_FULL = 1;
    public const KICK_FLAG_SERVER_WHITELISTED = 2;
    public const KICK_FLAG_BANNED = 3;
    public const KICK_FLAG_PRIORITY = [
        self::KICK_FLAG_PLUGIN,
        self::KICK_FLAG_SERVER_FULL,
        self::KICK_FLAG_SERVER_WHITELISTED,
        self::KICK_FLAG_BANNED,
    ];

    /** @var array<int, Translatable|string> */
    protected array $disconnectReasons = [];
    /** @var array<int, Translatable|string> */
    protected array $disconnectScreenMessages = [];

    public function __construct(
        private PlayerInfo $playerInfo,
        private string $ip,
        private int $port,
        protected bool $authRequired
    ) {
    }

    public function getPlayerInfo(): PlayerInfo { return $this->playerInfo; }
    public function getIp(): string { return $this->ip; }
    public function getPort(): int { return $this->port; }
    public function isAuthRequired(): bool { return $this->authRequired; }
    public function setAuthRequired(bool $v): void { $this->authRequired = $v; }
    /** @return list<int> */
    public function getKickFlags(): array { return array_keys($this->disconnectReasons); }
    public function isKickFlagSet(int $flag): bool { return isset($this->disconnectReasons[$flag]); }
    public function setKickFlag(int $flag, Translatable|string $disconnectReason, Translatable|string|null $disconnectScreenMessage = null): void
    {
        $this->disconnectReasons[$flag] = $disconnectReason;
        $this->disconnectScreenMessages[$flag] = $disconnectScreenMessage ?? $disconnectReason;
    }
    public function clearKickFlag(int $flag): void { unset($this->disconnectReasons[$flag], $this->disconnectScreenMessages[$flag]); }
    public function clearAllKickFlags(): void { $this->disconnectReasons = []; $this->disconnectScreenMessages = []; }
    public function isAllowed(): bool { return $this->disconnectReasons === []; }
    public function getDisconnectReason(int $flag): Translatable|string|null { return $this->disconnectReasons[$flag] ?? null; }
    public function getDisconnectScreenMessage(int $flag): Translatable|string|null { return $this->disconnectScreenMessages[$flag] ?? null; }
    public function getFinalDisconnectReason(): Translatable|string
    {
        foreach (self::KICK_FLAG_PRIORITY as $flag) {
            if (isset($this->disconnectReasons[$flag])) {
                return $this->disconnectReasons[$flag];
            }
        }
        return '';
    }
    public function getFinalDisconnectScreenMessage(): Translatable|string
    {
        foreach (self::KICK_FLAG_PRIORITY as $flag) {
            if (isset($this->disconnectScreenMessages[$flag])) {
                return $this->disconnectScreenMessages[$flag];
            }
        }
        return '';
    }
}
