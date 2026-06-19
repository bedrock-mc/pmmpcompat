<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\BootstrapOptions;
use pocketmine\GarbageCollectorManager;
use pocketmine\MemoryDump;
use pocketmine\MemoryManager;
use pocketmine\ServerConfigGroup;
use pocketmine\ServerProperties;
use pocketmine\TimeTrackingSleeperHandler;
use pocketmine\utils\Config;
use pocketmine\utils\VersionString;
use pocketmine\VersionInfo;
use pocketmine\YmlServerProperties;

$dir = sys_get_temp_dir() . '/pmmpcompat-root-smoke-' . getmypid();
@mkdir($dir, 0777, true);

assert(BootstrapOptions::NO_WIZARD === 'no-wizard');
assert(ServerProperties::SERVER_PORT_IPV4 === 'server-port');
assert(YmlServerProperties::MEMORY_MAIN_LIMIT === 'memory.main-limit');
assert(VersionInfo::NAME === 'PocketMine-MP');
assert(VersionInfo::VERSION() instanceof VersionString);
assert(VersionInfo::BUILD_NUMBER() === 0);
$gitHash = VersionInfo::GIT_HASH();
assert(preg_match('/^[a-f0-9]{40}(-dirty)?$/', $gitHash) === 1 || $gitHash === str_repeat('0', 40));

$pocketmineYml = new Config($dir . '/pocketmine.yml', Config::YAML, [
    'memory' => ['main-limit' => 128],
    'settings' => ['shutdown-message' => 'bye'],
]);
$serverProperties = new Config($dir . '/server.properties', Config::PROPERTIES, [
    ServerProperties::MOTD => 'Compat',
    ServerProperties::MAX_PLAYERS => 24,
    ServerProperties::ENABLE_QUERY => 'on',
]);
$group = new ServerConfigGroup($pocketmineYml, $serverProperties);
assert($group->getPropertyInt(YmlServerProperties::MEMORY_MAIN_LIMIT, 0) === 128);
assert($group->getPropertyString(YmlServerProperties::SETTINGS_SHUTDOWN_MESSAGE, '') === 'bye');
assert($group->getConfigString(ServerProperties::MOTD) === 'Compat');
assert($group->getConfigInt(ServerProperties::MAX_PLAYERS) === 24);
assert($group->getConfigBool(ServerProperties::ENABLE_QUERY) === true);
$group->setConfigString(ServerProperties::MOTD, 'Changed');
$group->setConfigInt(ServerProperties::MAX_PLAYERS, 32);
$group->setConfigBool(ServerProperties::WHITELIST, true);
$group->save();
$savedProperties = new Config($dir . '/server.properties', Config::PROPERTIES);
assert($savedProperties->get(ServerProperties::MOTD) === 'Changed');
assert((int) $savedProperties->get(ServerProperties::MAX_PLAYERS) === 32);
assert($savedProperties->get(ServerProperties::WHITELIST) === 1);

$gc = new GarbageCollectorManager();
assert($gc->getThreshold() > 0);
assert($gc->getCollectionTimeTotalNs() >= 0);
assert($gc->maybeCollectCycles() >= 0);

$memory = new MemoryManager();
assert($memory->isLowMemory() === false);
assert($memory->canUseChunkCache() === true);
assert($memory->getGlobalMemoryLimit() === 0);
assert($memory->getViewDistance(12) === 12);
$memory->trigger(10, 1);
assert($memory->isLowMemory() === true);
assert($memory->getViewDistance(12) === 4);

$dumpDir = $dir . '/dump';
$logger = new class {
    public array $messages = [];
    public function info(mixed $message): void { $this->messages[] = (string) $message; }
};
MemoryDump::dumpMemory(['root' => true], $dumpDir, 2, 64, $logger);
assert(is_file($dumpDir . '/serverEntry.js'));
assert(is_file($dumpDir . '/objects.js'));
assert($logger->messages === ['Finished!']);
MemoryManager::dumpMemory(new stdClass(), $dir . '/dump-static', 1, 16, $logger);
assert(is_file($dir . '/dump-static/serverEntry.js'));

$sleeper = new TimeTrackingSleeperHandler();
$hits = 0;
$entry = $sleeper->addNotifier(static function () use (&$hits): void { $hits++; });
$sleeper->processNotifications();
assert($hits === 1);
assert($sleeper->getNotificationProcessingTime() > 0);
$sleeper->resetNotificationProcessingTime();
assert($sleeper->getNotificationProcessingTime() === 0);
$entry->remove();
$sleeper->processNotifications();
assert($hits === 1);

echo "pmmpcompat root smoke ok\n";
