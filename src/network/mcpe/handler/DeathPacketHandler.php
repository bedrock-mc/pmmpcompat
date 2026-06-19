<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Restricts packet handling while the player is dead.
 */
class DeathPacketHandler extends PacketHandler
{
    private bool $respawned = false;

    public function __construct(private mixed $player = null, private ?\Closure $onRespawn = null) {}

    public function setUp(): void
    {
        parent::setUp();
    }

    public function handleContainerClose(mixed $packet = null): bool
    {
        return true;
    }

    public function handlePlayerAction(mixed $packet = null): bool
    {
        return true;
    }

    public function handleRespawn(mixed $packet = null): bool
    {
        $this->respawned = true;
        ($this->onRespawn ?? static fn () => null)($packet, $this->player);
        return true;
    }

    public function hasRespawned(): bool
    {
        return $this->respawned;
    }
}
