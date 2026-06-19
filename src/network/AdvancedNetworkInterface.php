<?php

declare(strict_types=1);

namespace pocketmine\network;

interface AdvancedNetworkInterface extends NetworkInterface
{
    public function blockAddress(string $address, int $timeout = 300): void;

    public function unblockAddress(string $address): void;

    public function setNetwork(Network $network): void;

    public function sendRawPacket(string $address, int $port, string $payload): void;

    public function addRawPacketFilter(string $regex): void;
}
