<?php

declare(strict_types=1);

namespace pocketmine\thread\log;

abstract class ThreadSafeLoggerAttachment
{
    abstract public function log(string $level, string $message): void;
}
