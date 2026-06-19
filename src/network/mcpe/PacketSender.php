<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

interface PacketSender
{
    public function send(string $payload, bool $immediate, ?int $receiptId): void;

    public function close(string $reason = 'unknown reason'): void;
}
