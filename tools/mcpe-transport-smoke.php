<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\network\mcpe\cache\ChunkCache;
use pocketmine\network\mcpe\compression\CompressBatchPromise;
use pocketmine\network\mcpe\compression\CompressBatchTask;
use pocketmine\network\mcpe\compression\DecompressionException;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\encryption\DecryptionException;
use pocketmine\network\mcpe\encryption\EncryptionContext;
use pocketmine\network\mcpe\raklib\PthreadsChannelReader;
use pocketmine\network\mcpe\raklib\PthreadsChannelWriter;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\mcpe\raklib\RakLibPacketSender;
use pocketmine\network\mcpe\raklib\RakLibServer;
use pocketmine\network\mcpe\raklib\SnoozeAwarePthreadsChannelWriter;

$compressor = new ZlibCompressor(level: 6, minCompressionSize: 1, maxDecompressionSize: 1024);
$compressed = $compressor->compress('dragonfly-local-state');
assert($compressor->decompress($compressed) === 'dragonfly-local-state');
assert($compressor->getCompressionThreshold() === 1);

try {
    $compressor->decompress('not zlib');
    assert(false);
} catch (DecompressionException) {
    assert(true);
}

$promise = new CompressBatchPromise();
$resolved = false;
$promise->onResolve(static function (CompressBatchPromise $promise) use (&$resolved): void {
    $resolved = $promise->getResult() !== '';
});
$task = new CompressBatchTask('batch-payload', $promise, $compressor);
$task->onRun();
$task->onCompletion();
assert($promise->hasResult() && $resolved);
assert($compressor->decompress($promise->getResult()) === 'batch-payload');

$buffer = [];
$writer = new PthreadsChannelWriter($buffer);
$reader = new PthreadsChannelReader($buffer);
$writer->write('one');
assert($reader->read() === 'one');
assert($reader->read() === null);

$wakeups = 0;
$notifier = new class($wakeups) {
    public function __construct(private int &$wakeups) {}
    public function wakeupSleeper(): void { ++$this->wakeups; }
};
$snoozeWriter = new SnoozeAwarePthreadsChannelWriter($buffer, $notifier);
$snoozeWriter->write('two');
assert($wakeups === 1);
assert((new PthreadsChannelReader($buffer))->read() === 'two');

$interface = new RakLibInterface();
$interface->start();
$interface->setName('Compat RakLib');
$interface->blockAddress('203.0.113.1', 10);
$interface->addRawPacketFilter('/^x/');
$interface->sendRawPacket('127.0.0.1', 19132, 'raw');
$interface->onClientConnect(7, '127.0.0.1', 19132, 99);
assert($interface->isStarted());
assert($interface->getName() === 'Compat RakLib');
assert(isset($interface->getBlockedAddresses()['203.0.113.1']));
assert($interface->getRawPacketFilters() === ['/^x/']);
assert($interface->getSentRawPackets()[0]['payload'] === 'raw');
assert(isset($interface->getSessions()[7]));

$sender = new RakLibPacketSender(7, $interface);
$sender->send('payload', true, 123);
assert($interface->getSentPackets()[0]['payload'] === 'payload');
$sender->close();
assert(!isset($interface->getSessions()[7]));

$server = new RakLibServer();
assert($server->getThreadName() === 'RakLib');
$server->startAndWait();
assert($server->isRunning());
$server->quit();
assert(!$server->isRunning());

$key = str_repeat('k', 32);
$encryption = EncryptionContext::fakeGCM($key);
$decryption = EncryptionContext::fakeGCM($key);
assert($decryption->decrypt($encryption->encrypt('secret')) === 'secret');
try {
    $decryption->decrypt('short');
    assert(false);
} catch (DecryptionException) {
    assert(true);
}

$world = new stdClass();
$cache = ChunkCache::getInstance($world, $compressor);
assert($cache->request(1, 2) === '');
assert($cache->request(1, 2) === '');
assert($cache->getHitPercentage() > 0.0);
$cache->onChunkChanged(1, 2);
assert($cache->calculateCacheSize() === 0);

echo "mcpe transport smoke ok\n";
