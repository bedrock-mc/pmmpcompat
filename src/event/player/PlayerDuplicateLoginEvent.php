<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\NetworkSession;

class PlayerDuplicateLoginEvent extends Event implements Cancellable
{
    use CancellableTrait;
    use PlayerDisconnectEventTrait;

    public function __construct(
        private NetworkSession $connectingSession,
        private NetworkSession $existingSession,
        private Translatable|string $disconnectReason,
        private Translatable|string|null $disconnectScreenMessage,
    ) {}

    public function getConnectingSession(): NetworkSession { return $this->connectingSession; }
    public function getExistingSession(): NetworkSession { return $this->existingSession; }
}
