<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\lang\Translatable;

trait PlayerDisconnectEventTrait
{
    public function setDisconnectReason(Translatable|string $disconnectReason): void
    {
        $this->disconnectReason = $disconnectReason;
    }

    public function getDisconnectReason(): Translatable|string
    {
        return $this->disconnectReason;
    }

    public function setDisconnectScreenMessage(Translatable|string|null $disconnectScreenMessage): void
    {
        $this->disconnectScreenMessage = $disconnectScreenMessage;
    }

    public function getDisconnectScreenMessage(): Translatable|string|null
    {
        return $this->disconnectScreenMessage ?? $this->disconnectReason;
    }
}
