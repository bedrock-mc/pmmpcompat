<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\utils\MainLogger;

class PluginLogger extends MainLogger
{
    /** @var array<int, \Closure> */
    private array $attachments = [];

    public function addAttachment(\Closure $attachment): void
    {
        $this->attachments[spl_object_id($attachment)] = $attachment;
    }

    public function removeAttachment(\Closure $attachment): void
    {
        unset($this->attachments[spl_object_id($attachment)]);
    }

    public function removeAttachments(): void
    {
        $this->attachments = [];
    }

    /** @return array<int, \Closure> */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function log(mixed $level, mixed $message): void
    {
        parent::log($level, $message);
        foreach ($this->attachments as $attachment) {
            $attachment($level, $message);
        }
    }
}
