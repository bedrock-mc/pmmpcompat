<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Tracks the client while it waits for chunks before spawn completion.
 */
class PreSpawnPacketHandler extends PacketHandler
{
    private ?int $chunkRadius = null;

    public function __construct(private mixed $session = null, private ?\Closure $onReady = null) {}

    public function setUp(): void
    {
        parent::setUp();
    }

    public function handlePlayerAuthInput(mixed $packet): bool
    {
        ($this->onReady ?? static fn () => null)($packet, $this->session);
        return true;
    }

    public function handleRequestChunkRadius(mixed $packet): bool
    {
        $this->chunkRadius = $this->readRadius($packet);
        return true;
    }

    public function getChunkRadius(): ?int
    {
        return $this->chunkRadius;
    }

    private function readRadius(mixed $packet): ?int
    {
        if (is_array($packet) && isset($packet['radius'])) {
            return (int) $packet['radius'];
        }
        if (is_object($packet)) {
            if (isset($packet->radius)) {
                return (int) $packet->radius;
            }
            if (method_exists($packet, 'getRadius')) {
                return (int) $packet->getRadius();
            }
        }
        return null;
    }
}
