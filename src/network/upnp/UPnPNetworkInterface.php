<?php

declare(strict_types=1);

namespace pocketmine\network\upnp;

use pocketmine\network\NetworkInterface;
use function method_exists;

final class UPnPNetworkInterface implements NetworkInterface
{
    private ?string $serviceURL = null;
    private string $name = '';
    private bool $started = false;

    public function __construct(
        private mixed $logger = null,
        private string $ip = '0.0.0.0',
        private int $port = 19132
    ) {
    }

    public function start(): void
    {
        $this->started = true;
        try {
            $this->serviceURL = UPnP::getServiceUrl();
        } catch (UPnPException $e) {
            $this->log('debug', 'UPnP portforward unavailable: ' . $e->getMessage());
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function tick(): void
    {
    }

    public function shutdown(): void
    {
        if ($this->serviceURL !== null) {
            UPnP::removePortForward($this->serviceURL, $this->port);
            $this->serviceURL = null;
        }
        $this->started = false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    private function log(string $level, string $message): void
    {
        if (is_object($this->logger) && method_exists($this->logger, $level)) {
            $this->logger->{$level}($message);
        }
    }
}
