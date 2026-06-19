<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\data\java\GameModeIdMap;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\form\FormValidationException;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\stats\SendUsageTask;
use pocketmine\wizard\SetupWizard;

assert(new SavedDataLoadingException('saved') instanceof RuntimeException);
assert(new FormValidationException('form') instanceof RuntimeException);

$map = new GameModeIdMap();
assert($map->fromId(0)?->equals(GameMode::SURVIVAL()) === true);
assert($map->fromId(1)?->equals(GameMode::CREATIVE()) === true);
assert($map->fromId(99) === null);
assert($map->toId(GameMode::ADVENTURE()) === 2);
assert(GameModeIdMap::getInstance() instanceof GameModeIdMap);

$server = new Server();
$open = new SendUsageTask($server, SendUsageTask::TYPE_OPEN, ['player-one']);
assert($open->endpoint === 'http://stats.pocketmine.net/api/post');
$payload = json_decode($open->data, true, flags: JSON_THROW_ON_ERROR);
assert($payload['event'] === 'open');
assert($payload['software'] === 'PocketMine-MP');
$status = new SendUsageTask($server, SendUsageTask::TYPE_STATUS, ['player-one']);
assert(json_decode($status->data, true, flags: JSON_THROW_ON_ERROR)['event'] === 'status');
$status->onRun();

$dir = sys_get_temp_dir() . '/pmmpcompat-wizard-' . getmypid();
$wizard = new SetupWizard($dir);
assert(SetupWizard::DEFAULT_PORT === Server::DEFAULT_PORT_IPV4);
assert($wizard->run() === true);
assert(is_dir($dir));

echo "pmmpcompat misc smoke ok\n";
