<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ServerboundPacket;

class DataPacketReceiveEvent extends ServerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(private NetworkSession $origin, private ServerboundPacket $packet) {}

    public function getPacket(): ServerboundPacket { return $this->packet; }
    public function getOrigin(): NetworkSession { return $this->origin; }
}
