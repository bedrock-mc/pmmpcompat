<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\updater\UpdateChecker;

class UpdateNotifyEvent extends ServerEvent
{
    public function __construct(private UpdateChecker $updater) {}

    public function getUpdater(): UpdateChecker { return $this->updater; }
}
