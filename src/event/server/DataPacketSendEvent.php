<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\utils\Utils;

class DataPacketSendEvent extends ServerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(private array $targets, private array $packets) {}

    public function getTargets(): array { return $this->targets; }
    public function getPackets(): array { return $this->packets; }
    public function setPackets(array $packets): void
    {
        Utils::validateArrayValueType($packets, fn(ClientboundPacket $_) => null);
        $this->packets = $packets;
    }
}
