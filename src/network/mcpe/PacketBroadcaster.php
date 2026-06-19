<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

interface PacketBroadcaster
{
    /** @param NetworkSession[] $recipients @param array<int, mixed> $packets */
    public function broadcastPackets(array $recipients, array $packets): void;
}
