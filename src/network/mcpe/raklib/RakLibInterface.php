<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\raklib;

class RakLibInterface
{
    private bool $started = false;
    private string $name = '';
    private mixed $network = null;
    /** @var array<int, object> */
    private array $sessions = [];
    /** @var array<string, int> */
    private array $blockedAddresses = [];
    /** @var string[] */
    private array $rawPacketFilters = [];
    /** @var list<array{sessionId: int, payload: string, immediate: bool, receiptId: ?int}> */
    private array $sentPackets = [];
    /** @var list<array{address: string, port: int, payload: string}> */
    private array $sentRawPackets = [];
    private ?int $packetLimit = null;
    private bool $portCheck = true;

    public function __construct(mixed ...$args) {}
    public function start(): void { $this->started = true; }
    public function tick(): void {}
    public function shutdown(): void { $this->started = false; $this->sessions = []; }
    public function setName(string $name): void { $this->name = $name; }
    public function setNetwork(mixed $network): void { $this->network = $network; }
    public function blockAddress(string $address, int $timeout = 300): void { $this->blockedAddresses[$address] = $timeout; }
    public function unblockAddress(string $address): void { unset($this->blockedAddresses[$address]); }
    public function addRawPacketFilter(string $regex): void { $this->rawPacketFilters[] = $regex; }
    public function sendRawPacket(string $address, int $port, string $payload): void
    {
        $this->sentRawPackets[] = compact('address', 'port', 'payload');
    }
    public function close(int $sessionId): void { unset($this->sessions[$sessionId]); }
    public function putPacket(int $sessionId, string $payload, bool $immediate = false, ?int $receiptId = null): void
    {
        $this->sentPackets[] = compact('sessionId', 'payload', 'immediate', 'receiptId');
    }
    public function setPacketLimit(int $limit): void { $this->packetLimit = $limit; }
    public function setPortCheck(bool $value): void { $this->portCheck = $value; }
    public function onClientConnect(int $sessionId, string $address, int $port, int $clientID): void
    {
        $this->sessions[$sessionId] = (object) compact('sessionId', 'address', 'port', 'clientID');
    }
    public function onClientDisconnect(int $sessionId, int $reason = 0): void { unset($this->sessions[$sessionId]); }
    public function onPacketReceive(int $sessionId, string $packet): void {}
    public function onRawPacketReceive(string $address, int $port, string $payload): void {}
    public function onPacketAck(int $sessionId, int $identifierACK): void {}
    public function onPingMeasure(int $sessionId, int $pingMS): void {}
    public function onBandwidthStatsUpdate(int $bytesSentDiff, int $bytesReceivedDiff): void {}

    public function isStarted(): bool { return $this->started; }
    public function getName(): string { return $this->name; }
    public function getNetwork(): mixed { return $this->network; }
    /** @return array<int, object> */
    public function getSessions(): array { return $this->sessions; }
    /** @return list<array{sessionId: int, payload: string, immediate: bool, receiptId: ?int}> */
    public function getSentPackets(): array { return $this->sentPackets; }
    /** @return list<array{address: string, port: int, payload: string}> */
    public function getSentRawPackets(): array { return $this->sentRawPackets; }
    /** @return string[] */
    public function getRawPacketFilters(): array { return $this->rawPacketFilters; }
    /** @return array<string, int> */
    public function getBlockedAddresses(): array { return $this->blockedAddresses; }
    public function getPacketLimit(): ?int { return $this->packetLimit; }
    public function getPortCheck(): bool { return $this->portCheck; }
}
