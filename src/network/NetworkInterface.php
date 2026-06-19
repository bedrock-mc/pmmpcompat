<?php

declare(strict_types=1);

namespace pocketmine\network;

interface NetworkInterface
{
    public function start(): void;

    public function setName(string $name): void;

    public function tick(): void;

    public function shutdown(): void;
}
