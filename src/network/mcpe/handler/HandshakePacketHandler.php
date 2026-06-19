<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Handler responsible for awaiting client response from the crypto handshake.
 */
class HandshakePacketHandler extends PacketHandler
{
    private bool $completed = false;

    public function __construct(private ?\Closure $onHandshakeCompleted = null) {}

    public function handleClientToServerHandshake(mixed $packet = null): bool
    {
        if ($this->completed) {
            return true;
        }

        $this->completed = true;
        ($this->onHandshakeCompleted ?? static fn () => null)($packet);
        return true;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }
}
