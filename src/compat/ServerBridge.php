<?php

declare(strict_types=1);

namespace pocketmine\compat;

interface ServerBridge
{
    public function broadcastMessage(string $message): void;
}
