<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\network\mcpe\NetworkSession;

class DataPacketDecodeEvent extends ServerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(private NetworkSession $origin, private int $packetId, private string $packetBuffer) {}

    public function getOrigin(): NetworkSession { return $this->origin; }
    public function getPacketId(): int { return $this->packetId; }
    public function getPacketBuffer(): string { return $this->packetBuffer; }
}
