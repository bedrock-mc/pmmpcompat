<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class NetworkInterfaceRegisterEvent extends NetworkInterfaceEvent implements Cancellable
{
    use CancellableTrait;
}
