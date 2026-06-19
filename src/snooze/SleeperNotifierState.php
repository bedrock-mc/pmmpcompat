<?php

declare(strict_types=1);

namespace pocketmine\snooze;

final class SleeperNotifierState extends \pmmp\thread\ThreadSafe
{
    public \pmmp\thread\ThreadSafeArray $pending;

    public function __construct()
    {
        $this->pending = new \pmmp\thread\ThreadSafeArray();
    }
}
