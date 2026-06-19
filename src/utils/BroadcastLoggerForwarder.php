<?php

declare(strict_types=1);

namespace pocketmine\utils;

class BroadcastLoggerForwarder
{
    private int $screenLineHeight = PHP_INT_MAX;

    public function __construct(
        private mixed $server,
        private mixed $logger,
        private mixed $language,
    ) {
    }

    public function getLanguage(): mixed
    {
        return $this->language;
    }

    public function sendMessage(mixed $message): void
    {
        if (is_object($message) && method_exists($this->language, 'translate')) {
            $message = $this->language->translate($message);
        }
        if (is_object($this->logger) && method_exists($this->logger, 'info')) {
            $this->logger->info((string) $message);
        }
    }

    public function getServer(): mixed
    {
        return $this->server;
    }

    public function getName(): string
    {
        return 'Broadcast Logger Forwarder';
    }

    public function getScreenLineHeight(): int
    {
        return $this->screenLineHeight;
    }

    public function setScreenLineHeight(?int $height): void
    {
        $this->screenLineHeight = $height ?? PHP_INT_MAX;
    }
}
