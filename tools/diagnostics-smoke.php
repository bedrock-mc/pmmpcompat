<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\console\ConsoleReaderChildProcessDaemon;
use pocketmine\console\ConsoleReaderChildProcessUtils;
use pocketmine\crash\CrashDump;
use pocketmine\crash\CrashDumpData;
use pocketmine\crash\CrashDumpDataGeneral;
use pocketmine\crash\CrashDumpDataPluginEntry;
use pocketmine\crash\CrashDumpRenderer;
use pocketmine\Server;
use pocketmine\updater\UpdateChecker;
use pocketmine\updater\UpdateInfo;

assert(ConsoleReaderChildProcessUtils::TOKEN_DELIMITER === ':');
$counter = 42;
$message = ConsoleReaderChildProcessUtils::createMessage('say hello', $counter);
assert($counter === 43);
$parseCounter = 42;
assert(ConsoleReaderChildProcessUtils::parseMessage($message, $parseCounter) === 'say hello');
assert($parseCounter === 43);
assert(ConsoleReaderChildProcessUtils::parseMessage('say hello:bad-token', $parseCounter) === null);
$daemon = new ConsoleReaderChildProcessDaemon();
assert($daemon->readLine() === null);
$daemon->quit();

$general = new CrashDumpDataGeneral('PocketMine-MP', '5.0.0', 12, false, 0, str_repeat('a', 40), 'uname', PHP_VERSION, zend_version(), PHP_OS, PHP_OS_FAMILY, ['lib' => '1.0.0']);
assert($general->composer_libraries['lib'] === '1.0.0');
$plugin = new CrashDumpDataPluginEntry('Plugin', '1.0.0', ['dev'], ['5.0.0'], true, [], [], 'Plugin\\Main', 'POSTWORLD', '');
assert($plugin->enabled === true);
$data = new CrashDumpData();
$data->time = 1.0;
$data->uptime = 0.5;
$data->general = $general;
$data->error = ['message' => 'boom', 'file' => 'file.php', 'line' => 9, 'type' => 'RuntimeException'];
$data->trace = ['#0 smoke'];
$data->plugins['Plugin'] = $plugin;
$data->serverDotProperties = 'motd=secret';
$serialized = $data->jsonSerialize();
assert($serialized['server.properties'] === 'motd=secret');
assert(!array_key_exists('serverDotProperties', $serialized));
$fp = fopen('php://temp', 'w+');
$renderer = new CrashDumpRenderer($fp, $data);
$renderer->renderHumanReadable();
rewind($fp);
$rendered = stream_get_contents($fp);
assert(str_contains($rendered, 'PocketMine-MP Crash Dump'));
assert(str_contains($rendered, 'Error: boom'));
fclose($fp);

$server = new Server();
$dump = new CrashDump($server, $server->getPluginManager());
assert($dump->getData()->general->name === 'PocketMine-MP');
assert($dump->getEncodedData() !== '');
$fp = fopen('php://temp', 'w+');
$dumpRenderer = new CrashDumpRenderer($fp, $dump->getData());
$dump->encodeData($dumpRenderer);
rewind($fp);
assert(str_contains(stream_get_contents($fp), '===BEGIN CRASH DUMP==='));
fclose($fp);

$checker = new UpdateChecker($server, 'updates.example');
assert($checker->getEndpoint() === 'http://updates.example/api/');
assert($checker->getChannel() === 'stable');
assert($checker->hasUpdate() === false);
$info = new UpdateInfo();
$info->base_version = '999.0.0';
$info->build = 999;
$info->is_dev = false;
$info->date = 1;
$checker->checkUpdateCallback($info);
assert($checker->hasUpdate() === true);
assert($checker->getUpdateInfo() === $info);
$checker->showConsoleUpdate();

echo "pmmpcompat diagnostics smoke ok\n";
