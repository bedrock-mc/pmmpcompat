<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

class StandardPacketBroadcaster implements PacketBroadcaster
{
    /** @var list<array{recipients: array<int, mixed>, packets: array<int, mixed>}> */
    private array $broadcasts = [];

    public function __construct() {}

    /** @param NetworkSession[] $recipients @param array<int, mixed> $packets */
    public function broadcastPackets(array $recipients, array $packets): void
    {
        $this->broadcasts[] = ['recipients' => $recipients, 'packets' => $packets];
        foreach ($recipients as $recipient) {
            if ($recipient instanceof NetworkSession) {
                foreach ($packets as $packet) {
                    $recipient->sendDataPacket($packet);
                }
            }
        }
    }

    /** @return list<array{recipients: array<int, mixed>, packets: array<int, mixed>}> */
    public function getBroadcasts(): array { return $this->broadcasts; }
}
