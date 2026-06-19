<?php

declare(strict_types=1);

namespace pocketmine\compat;

use pocketmine\form\Form;
use pocketmine\world\Position;

class HostActionQueue implements ServerBridge
{
    /** @var list<array<string, mixed>> */
    private array $actions = [];
    /** @var array<string, PlayerBridge> */
    private array $players = [];

    public function forPlayer(string $uuid): PlayerBridge
    {
        return $this->players[$uuid] ??= new QueuedPlayerBridge($uuid, $this);
    }

    public function broadcastMessage(string $message): void
    {
        $this->push(['type' => 'server.broadcast_message', 'message' => $message]);
    }

    /** @param array<string, mixed> $action */
    public function push(array $action): void
    {
        $this->actions[] = $action;
    }

    /** @return list<array<string, mixed>> */
    public function drain(): array
    {
        $actions = $this->actions;
        $this->actions = [];
        return $actions;
    }
}
