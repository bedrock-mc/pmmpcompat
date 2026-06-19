<?php

declare(strict_types=1);

namespace pocketmine\thread\log;

abstract class AttachableThreadSafeLogger extends ThreadSafeLogger
{
    /** @var array<int, ThreadSafeLoggerAttachment> */
    private array $attachments = [];

    public function __construct()
    {
    }

    public function addAttachment(ThreadSafeLoggerAttachment $attachment): void
    {
        $this->attachments[spl_object_id($attachment)] = $attachment;
    }

    public function removeAttachment(ThreadSafeLoggerAttachment $attachment): void
    {
        unset($this->attachments[spl_object_id($attachment)]);
    }

    public function removeAttachments(): void
    {
        $this->attachments = [];
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function log(mixed $level, mixed $message): void
    {
        foreach ($this->attachments as $attachment) {
            $attachment->log((string) $level, (string) $message);
        }
    }
}
