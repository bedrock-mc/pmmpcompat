<?php

declare(strict_types=1);

namespace pocketmine\network;

use function base64_encode;
use function get_class;
use function method_exists;
use function preg_match;
use function spl_object_id;
use function time;
use const PHP_INT_MAX;

class Network
{
    /** @var array<int, NetworkInterface> */
    private array $interfaces = [];
    /** @var array<int, AdvancedNetworkInterface> */
    private array $advancedInterfaces = [];
    /** @var array<int, RawPacketHandler> */
    private array $rawPacketHandlers = [];
    /** @var array<string, int> */
    private array $bannedIps = [];

    private BidirectionalBandwidthStatsTracker $bandwidthTracker;
    private string $name = '';
    private NetworkSessionManager $sessionManager;

    public function __construct(private mixed $logger = null)
    {
        $this->sessionManager = new NetworkSessionManager();
        $this->bandwidthTracker = new BidirectionalBandwidthStatsTracker(5);
    }

    public function getBandwidthTracker(): BidirectionalBandwidthStatsTracker
    {
        return $this->bandwidthTracker;
    }

    /** @return array<int, NetworkInterface> */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    public function getSessionManager(): NetworkSessionManager
    {
        return $this->sessionManager;
    }

    public function getConnectionCount(): int
    {
        return $this->sessionManager->getSessionCount();
    }

    public function getValidConnectionCount(): int
    {
        return $this->sessionManager->getValidSessionCount();
    }

    public function tick(): void
    {
        foreach ($this->interfaces as $interface) {
            $interface->tick();
        }
        $this->sessionManager->tick();
    }

    public function registerInterface(NetworkInterface $interface): bool
    {
        $interface->start();
        $hash = spl_object_id($interface);
        $this->interfaces[$hash] = $interface;
        if ($interface instanceof AdvancedNetworkInterface) {
            $this->advancedInterfaces[$hash] = $interface;
            $interface->setNetwork($this);
            foreach ($this->bannedIps as $ip => $_until) {
                $interface->blockAddress($ip);
            }
            foreach ($this->rawPacketHandlers as $handler) {
                $interface->addRawPacketFilter($handler->getPattern());
            }
        }
        $interface->setName($this->name);
        return true;
    }

    public function unregisterInterface(NetworkInterface $interface): void
    {
        $hash = spl_object_id($interface);
        if (!isset($this->interfaces[$hash])) {
            throw new \InvalidArgumentException('Interface ' . get_class($interface) . ' is not registered on this network');
        }
        unset($this->interfaces[$hash], $this->advancedInterfaces[$hash]);
        $interface->shutdown();
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updateName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function updateName(): void
    {
        foreach ($this->interfaces as $interface) {
            $interface->setName($this->name);
        }
    }

    public function sendPacket(string $address, int $port, string $payload): void
    {
        foreach ($this->advancedInterfaces as $interface) {
            $interface->sendRawPacket($address, $port, $payload);
        }
    }

    public function blockAddress(string $address, int $timeout = 300): void
    {
        $this->bannedIps[$address] = $timeout > 0 ? time() + $timeout : PHP_INT_MAX;
        foreach ($this->advancedInterfaces as $interface) {
            $interface->blockAddress($address, $timeout);
        }
    }

    public function unblockAddress(string $address): void
    {
        unset($this->bannedIps[$address]);
        foreach ($this->advancedInterfaces as $interface) {
            $interface->unblockAddress($address);
        }
    }

    public function registerRawPacketHandler(RawPacketHandler $handler): void
    {
        $this->rawPacketHandlers[spl_object_id($handler)] = $handler;
        foreach ($this->advancedInterfaces as $interface) {
            $interface->addRawPacketFilter($handler->getPattern());
        }
    }

    public function unregisterRawPacketHandler(RawPacketHandler $handler): void
    {
        unset($this->rawPacketHandlers[spl_object_id($handler)]);
    }

    public function processRawPacket(AdvancedNetworkInterface $interface, string $address, int $port, string $packet): void
    {
        if (isset($this->bannedIps[$address]) && time() < $this->bannedIps[$address]) {
            $this->log('debug', "Dropped raw packet from banned address $address $port");
            return;
        }

        $handled = false;
        foreach ($this->rawPacketHandlers as $handler) {
            if (preg_match($handler->getPattern(), $packet) === 1) {
                try {
                    $handled = $handler->handle($interface, $address, $port, $packet);
                } catch (PacketHandlingException $e) {
                    $handled = true;
                    $this->log('error', "Bad raw packet from /$address:$port: " . $e->getMessage());
                    $this->blockAddress($address, 600);
                    break;
                }
            }
        }
        if (!$handled) {
            $this->log('debug', "Unhandled raw packet from /$address:$port: " . base64_encode($packet));
        }
    }

    private function log(string $level, string $message): void
    {
        if (is_object($this->logger) && method_exists($this->logger, $level)) {
            $this->logger->{$level}($message);
        }
    }
}
