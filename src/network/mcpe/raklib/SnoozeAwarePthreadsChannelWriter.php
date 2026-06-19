<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\raklib;

class SnoozeAwarePthreadsChannelWriter extends PthreadsChannelWriter
{
    public function __construct(mixed &$buffer, private mixed $notifier = null)
    {
        parent::__construct($buffer);
    }

    public function write(string $str): void
    {
        parent::write($str);
        if (is_object($this->notifier) && method_exists($this->notifier, 'wakeupSleeper')) {
            $this->notifier->wakeupSleeper();
        }
    }
}
