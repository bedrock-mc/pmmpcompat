<?php

declare(strict_types=1);

namespace pocketmine\network;

interface RawPacketHandler
{
    public function getPattern(): string;

    public function handle(AdvancedNetworkInterface $interface, string $address, int $port, string $packet): bool;
}
