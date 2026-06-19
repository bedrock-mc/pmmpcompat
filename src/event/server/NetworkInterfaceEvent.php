<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\network\NetworkInterface;

class NetworkInterfaceEvent extends ServerEvent
{
    public function __construct(protected NetworkInterface $interface) {}

    public function getInterface(): NetworkInterface { return $this->interface; }
}
