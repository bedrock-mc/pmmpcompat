<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

final class NetworkBroadcastUtils
{
    /** @param NetworkSession[] $recipients @param array<int, mixed> $packets */
    public static function broadcastPackets(array $recipients, array $packets): void
    {
        (new StandardPacketBroadcaster())->broadcastPackets($recipients, $packets);
    }

    /** @param NetworkSession[] $recipients */
    public static function broadcastEntityEvent(array $recipients, mixed $entity, mixed $event): void
    {
        (new StandardEntityEventBroadcaster())->syncActorData($recipients, $entity, ['event' => $event]);
    }
}
