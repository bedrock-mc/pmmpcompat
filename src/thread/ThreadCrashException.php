<?php

declare(strict_types=1);

namespace pocketmine\thread;

class ThreadCrashException extends ThreadException
{
    public function __construct(string $message, private ThreadCrashInfo $crashInfo)
    {
        parent::__construct($message);
    }

    public function getCrashInfo(): ThreadCrashInfo
    {
        return $this->crashInfo;
    }
}
