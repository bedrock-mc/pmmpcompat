<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\raklib;

use pocketmine\network\mcpe\PacketSender;

class RakLibPacketSender implements PacketSender
{
    private bool $closed = false;

    public function __construct(private int $sessionId, private RakLibInterface $handler) {}

    public function send(string $payload, bool $immediate, ?int $receiptId): void
    {
        if (!$this->closed) {
            $this->handler->putPacket($this->sessionId, $payload, $immediate, $receiptId);
        }
    }

    public function close(string $reason = 'unknown reason'): void
    {
        if (!$this->closed) {
            $this->closed = true;
            $this->handler->close($this->sessionId);
        }
    }
}
