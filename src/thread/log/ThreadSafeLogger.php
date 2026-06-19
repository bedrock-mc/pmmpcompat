<?php

declare(strict_types=1);

namespace pocketmine\thread\log;

abstract class ThreadSafeLogger
{
    abstract public function log(mixed $level, mixed $message): void;

    public function emergency(mixed $message): void { $this->log('emergency', $message); }
    public function alert(mixed $message): void { $this->log('alert', $message); }
    public function critical(mixed $message): void { $this->log('critical', $message); }
    public function error(mixed $message): void { $this->log('error', $message); }
    public function warning(mixed $message): void { $this->log('warning', $message); }
    public function notice(mixed $message): void { $this->log('notice', $message); }
    public function info(mixed $message): void { $this->log('info', $message); }
    public function debug(mixed $message): void { $this->log('debug', $message); }
}
