<?php

declare(strict_types=1);

namespace pocketmine\network\query;

use pocketmine\network\AdvancedNetworkInterface;
use pocketmine\network\Network;
use function preg_match;
use function time;
use const PHP_INT_MAX;

final class DedicatedQueryNetworkInterface implements AdvancedNetworkInterface
{
    private ?Network $network = null;
    /** @var array<string, int> */
    private array $blockedIps = [];
    /** @var string[] */
    private array $rawPacketPatterns = [];
    /** @var list<array{address: string, port: int, payload: string}> */
    private array $sentPackets = [];
    private string $name = '';
    private bool $started = false;

    public function __construct(
        private string $ip = '0.0.0.0',
        private int $port = 19132,
        private bool $ipV6 = false,
        private mixed $logger = null
    ) {
    }

    public function start(): void
    {
        $this->started = true;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function tick(): void
    {
    }

    public function blockAddress(string $address, int $timeout = 300): void
    {
        $this->blockedIps[$address] = $timeout > 0 ? time() + $timeout : PHP_INT_MAX;
    }

    public function unblockAddress(string $address): void
    {
        unset($this->blockedIps[$address]);
    }

    public function setNetwork(Network $network): void
    {
        $this->network = $network;
    }

    public function sendRawPacket(string $address, int $port, string $payload): void
    {
        $this->sentPackets[] = ['address' => $address, 'port' => $port, 'payload' => $payload];
    }

    public function addRawPacketFilter(string $regex): void
    {
        $this->rawPacketPatterns[] = $regex;
    }

    public function shutdown(): void
    {
        $this->started = false;
    }

    public function receiveRawPacket(string $address, int $port, string $payload): bool
    {
        if ($this->network === null || (isset($this->blockedIps[$address]) && time() < $this->blockedIps[$address])) {
            return false;
        }
        foreach ($this->rawPacketPatterns as $pattern) {
            if (preg_match($pattern, $payload) === 1) {
                $this->network->processRawPacket($this, $address, $port, $payload);
                return true;
            }
        }
        return false;
    }

    /** @return list<array{address: string, port: int, payload: string}> */
    public function getSentPackets(): array
    {
        return $this->sentPackets;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
