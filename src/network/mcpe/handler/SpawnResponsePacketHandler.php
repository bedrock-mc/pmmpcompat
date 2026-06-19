<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Waits for the client's local-player initialized acknowledgement.
 */
class SpawnResponsePacketHandler extends PacketHandler
{
    private bool $initialized = false;
    private mixed $lastSkinPacket = null;

    public function __construct(private mixed $session = null, private ?\Closure $onInitialized = null) {}

    public function handlePlayerAuthInput(mixed $packet): bool
    {
        return true;
    }

    public function handlePlayerSkin(mixed $packet): bool
    {
        $this->lastSkinPacket = $packet;
        return true;
    }

    public function handleSetLocalPlayerAsInitialized(mixed $packet = null): bool
    {
        if (!$this->initialized) {
            $this->initialized = true;
            ($this->onInitialized ?? static fn () => null)($packet, $this->session);
        }
        return true;
    }

    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    public function getLastSkinPacket(): mixed
    {
        return $this->lastSkinPacket;
    }
}
