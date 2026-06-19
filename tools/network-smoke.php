<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\network\AdvancedNetworkInterface;
use pocketmine\network\BandwidthStatsTracker;
use pocketmine\network\BidirectionalBandwidthStatsTracker;
use pocketmine\network\Network;
use pocketmine\network\NetworkInterface;
use pocketmine\network\NetworkSessionManager;
use pocketmine\network\PacketHandlingException;
use pocketmine\network\RawPacketHandler;
use pocketmine\network\query\DedicatedQueryNetworkInterface;
use pocketmine\network\query\QueryHandler;
use pocketmine\network\query\QueryInfo;
use pocketmine\network\upnp\UPnP;
use pocketmine\network\upnp\UPnPException;
use pocketmine\network\upnp\UPnPNetworkInterface;

$stats = new BandwidthStatsTracker(2);
$stats->add(12);
assert($stats->getTotalBytes() === 12);
$stats->rotateHistory();
$stats->add(6);
$stats->rotateHistory();
assert($stats->getAverageBytes() === 9.0);
$stats->resetHistory();
assert($stats->getAverageBytes() === 0.0);

$bidirectional = new BidirectionalBandwidthStatsTracker(1);
$bidirectional->add(4, 7);
assert($bidirectional->getSend()->getTotalBytes() === 4);
assert($bidirectional->getReceive()->getTotalBytes() === 7);

$session = new class {
    public bool $connected = true;
    public bool $ticked = false;
    public mixed $disconnectReason = null;
    public function tick(): void { $this->ticked = true; }
    public function isConnected(): bool { return $this->connected; }
    public function disconnect(mixed $reason = '', mixed $screen = null): void { $this->disconnectReason = $reason; }
};
$sessions = new NetworkSessionManager();
$sessions->add($session);
assert($sessions->getSessionCount() === 1 && $sessions->getValidSessionCount() === 0);
$sessions->markLoginReceived($session);
assert($sessions->getValidSessionCount() === 1);
$sessions->tick();
assert($session->ticked === true);
$sessions->close('done');
assert($session->disconnectReason === 'done' && $sessions->getSessionCount() === 0);

$interface = new class implements AdvancedNetworkInterface {
    public string $name = '';
    public bool $started = false;
    /** @var string[] */
    public array $filters = [];
    /** @var list<array{address: string, port: int, payload: string}> */
    public array $sent = [];
    public function start(): void { $this->started = true; }
    public function setName(string $name): void { $this->name = $name; }
    public function tick(): void {}
    public function shutdown(): void { $this->started = false; }
    public function blockAddress(string $address, int $timeout = 300): void {}
    public function unblockAddress(string $address): void {}
    public function setNetwork(Network $network): void {}
    public function sendRawPacket(string $address, int $port, string $payload): void { $this->sent[] = compact('address', 'port', 'payload'); }
    public function addRawPacketFilter(string $regex): void { $this->filters[] = $regex; }
};
$network = new Network();
$network->setName('Compat');
assert($network->registerInterface($interface) === true);
assert($interface->started === true && $interface->name === 'Compat');
$network->sendPacket('127.0.0.1', 19132, 'abc');
assert($interface->sent[0]['payload'] === 'abc');

$handled = false;
$handler = new class($handled) implements RawPacketHandler {
    public function __construct(private bool &$handled) {}
    public function getPattern(): string { return '/^raw$/'; }
    public function handle(AdvancedNetworkInterface $interface, string $address, int $port, string $packet): bool
    {
        $this->handled = true;
        return true;
    }
};
$network->registerRawPacketHandler($handler);
assert($interface->filters === ['/^raw$/']);
$network->processRawPacket($interface, '127.0.0.1', 19132, 'raw');
assert($handled === true);
assert(PacketHandlingException::wrap(new RuntimeException('bad'), 'prefix')->getMessage() === 'prefix: bad');

$queryInfo = new QueryInfo();
$queryInfo->setServerName('Lunar Compat');
$queryInfo->setWorld('world');
$queryInfo->setPlayerCount(1);
$queryInfo->setMaxPlayerCount(20);
$queryInfo->setPlayerList(['Steve']);
$queryInfo->setExtraData(['custom' => 'value']);
assert(str_contains($queryInfo->getLongQuery(), "hostname\x00Lunar Compat\x00"));
assert(str_contains($queryInfo->getLongQuery(), "player_\x00\x00Steve\x00"));
assert(str_contains($queryInfo->getShortQuery(), "Lunar Compat\x00SMP\x00world\x001\x0020\x00"));

$server = new class($queryInfo) {
    public function __construct(private QueryInfo $queryInfo) {}
    public function getQueryInformation(): QueryInfo { return $this->queryInfo; }
};
$queryNetwork = new Network();
$queryInterface = new DedicatedQueryNetworkInterface();
$queryHandler = new QueryHandler($server);
$queryNetwork->registerInterface($queryInterface);
$queryNetwork->registerRawPacketHandler($queryHandler);

$sessionId = 0x01020304;
$queryInterface->receiveRawPacket('127.0.0.1', 19132, "\xfe\xfd" . chr(QueryHandler::HANDSHAKE) . pack('N', $sessionId));
$sent = $queryInterface->getSentPackets();
assert(count($sent) === 1 && $sent[0]['payload'][0] === chr(QueryHandler::HANDSHAKE));
$token = (int) rtrim(substr($sent[0]['payload'], 5), "\x00");
$queryInterface->receiveRawPacket('127.0.0.1', 19132, "\xfe\xfd" . chr(QueryHandler::STATISTICS) . pack('N', $sessionId) . pack('N', $token & 0xffffffff) . "\xff\xff\xff\x01");
$sent = $queryInterface->getSentPackets();
assert(count($sent) === 2 && str_contains($sent[1]['payload'], "hostname\x00Lunar Compat\x00"));

$plainInterface = new class implements NetworkInterface {
    public bool $started = false;
    public function start(): void { $this->started = true; }
    public function setName(string $name): void {}
    public function tick(): void {}
    public function shutdown(): void { $this->started = false; }
};
$queryNetwork->registerInterface($plainInterface);
assert($plainInterface->started === true);
$queryNetwork->unregisterInterface($plainInterface);
assert($plainInterface->started === false);

try {
    UPnP::getServiceUrl();
    assert(false);
} catch (UPnPException) {
    assert(true);
}
$upnp = new UPnPNetworkInterface();
$upnp->start();
$upnp->setName('UPnP');
assert($upnp->isStarted() === true && $upnp->getName() === 'UPnP');
$upnp->shutdown();
assert($upnp->isStarted() === false);

echo "network smoke ok\n";
