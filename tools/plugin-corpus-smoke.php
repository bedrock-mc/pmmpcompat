<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\compat\HostActionQueue;
use pocketmine\compat\Runtime;
use pocketmine\item\VanillaItems;
use pocketmine\Server;

$root = sys_get_temp_dir() . '/pmmpcompat-corpus-' . getmypid();
removeTree($root);
mkdir($root . '/plugins/SupportPlugin/src/Corpus/Support', 0777, true);
mkdir($root . '/plugins/RuntimePlugin/src/Corpus/Runtime', 0777, true);
mkdir($root . '/plugins/RuntimePlugin/src/Corpus/Virion', 0777, true);
mkdir($root . '/plugins/RuntimePlugin/resources', 0777, true);

file_put_contents($root . '/plugins/SupportPlugin/plugin.yml', <<<'YAML'
name: SupportPlugin
main: Corpus\Support\SupportPlugin
version: 1.0.0
api: [5.0.0]
loadbefore: [RuntimePlugin]
YAML);

file_put_contents($root . '/plugins/SupportPlugin/src/Corpus/Support/SupportPlugin.php', <<<'PHP'
<?php
declare(strict_types=1);
namespace Corpus\Support;

use pocketmine\plugin\PluginBase;

final class SupportPlugin extends PluginBase{
    protected function onEnable() : void{
        $this->getServer()->getLogger()->info("support-enabled");
    }
}
PHP);

file_put_contents($root . '/plugins/RuntimePlugin/plugin.yml', <<<'YAML'
name: RuntimePlugin
main: Corpus\Runtime\RuntimePlugin
version: 1.0.0
api: [5.0.0]
softdepend: [SupportPlugin]
commands:
  corpus:
    description: Runtime corpus command
    aliases: [corp]
    permission: corpus.use
permissions:
  corpus.use:
    description: Use corpus command
YAML);

file_put_contents($root . '/plugins/RuntimePlugin/resources/config.yml', <<<'YAML'
enabled: true
message: bundled
YAML);

file_put_contents($root . '/plugins/RuntimePlugin/src/Corpus/Virion/MathBox.php', <<<'PHP'
<?php
declare(strict_types=1);
namespace Corpus\Virion;

final class MathBox{
    public static function score(string $name) : int{
        return strlen($name) * 7;
    }
}
PHP);

file_put_contents($root . '/plugins/RuntimePlugin/src/Corpus/Runtime/RuntimePlugin.php', <<<'PHP'
<?php
declare(strict_types=1);
namespace Corpus\Runtime;

use Corpus\Virion\MathBox;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\form\SimpleForm;
use pocketmine\item\VanillaItems;
use pocketmine\permission\Permission;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;

final class RuntimePlugin extends PluginBase implements Listener{
    public int $joins = 0;
    public int $formResponses = 0;
    public int $scheduledRuns = 0;
    public bool $asyncCompleted = false;
    private \SQLite3 $db;

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->db = new \SQLite3($this->getDataFolder() . "runtime.sqlite");
        $this->db->exec("CREATE TABLE IF NOT EXISTS joins(name TEXT, score INTEGER)");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPermissionManager()->addPermission(new Permission("corpus.use"));
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
            $this->scheduledRuns++;
            $this->getServer()->getAsyncPool()->collectTasks();
        }), 1);
        $this->getServer()->getAsyncPool()->submitTask(new class($this->getDataFolder()) extends AsyncTask{
            public function __construct(private string $dataFolder){}
            public function onRun() : void{
                file_put_contents($this->dataFolder . "async.txt", "done");
                $this->setResult("async-ok");
                $this->publishProgress(["phase" => "ran"]);
            }
            public function onCompletion() : void{
                file_put_contents($this->dataFolder . "async-complete.txt", (string) $this->getResult());
            }
        });
        $this->getServer()->getWorldManager()->generateWorld("corpus");
        $this->getConfig()->setNested("runtime.enabled", true);
        $this->saveConfig();
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $this->joins++;
        $score = MathBox::score($player->getName());
        $stmt = $this->db->prepare("INSERT INTO joins(name, score) VALUES(:name, :score)");
        $stmt->bindValue(":name", $player->getName(), SQLITE3_TEXT);
        $stmt->bindValue(":score", $score, SQLITE3_INTEGER);
        $stmt->execute();
        $player->addPermission("corpus.use");
        $player->sendMessage("joined:" . $score);
        $player->getInventory()->setItem(0, VanillaItems::DIAMOND()->setCount(2));
        $player->setHealth(18.0);
        $player->sendForm((new SimpleForm("Corpus", "Pick", function(Player $player, mixed $data) : void{
            $this->formResponses++;
            $player->sendMessage("form:" . $data);
        }))->addButton("OK"));
    }

    public function onChat(PlayerChatEvent $event) : void{
        $event->setMessage(strtoupper($event->getMessage()));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        $sender->sendMessage("cmd:" . $label . ":" . implode(",", $args));
        $this->getServer()->broadcastMessage("broadcast:" . $sender->getName());
        return true;
    }

    protected function onDisable() : void{
        $this->db->close();
        file_put_contents($this->getDataFolder() . "disabled.txt", "yes");
    }
}
PHP);

$queue = new HostActionQueue();
$runtime = new Runtime($root . '/plugins', new Server($queue));
$runtime->load();
$runtime->enable();

$plugins = array_map(static fn($plugin): string => $plugin->getName(), $runtime->plugins());
assertSame(['SupportPlugin', 'RuntimePlugin'], $plugins, 'load order honours loadbefore/softdepend');

$join = $runtime->playerJoin('uuid-corpus', 'Alex', $queue->forPlayer('uuid-corpus'));
assertSame('Alex', $join->getPlayer()->getName(), 'join returns player');
$actions = $queue->drain();
assertAction($actions, 'player.send_message', 'join message bridge action');
assertAction($actions, 'player.inventory.set_item', 'inventory bridge action');
assertAction($actions, 'player.set_health', 'health bridge action');
assertAction($actions, 'player.send_form', 'form bridge action');

$chat = $runtime->chat('uuid-corpus', 'Alex', 'hello db');
assertSame('HELLO DB', $chat->getMessage(), 'listener mutates chat');

assertSame(true, $runtime->command('uuid-corpus', 'Alex', 'corp', ['one', 'two']), 'alias command dispatches');
$actions = $queue->drain();
assertAction($actions, 'player.send_message', 'command message bridge action');
assertAction($actions, 'server.broadcast_message', 'server broadcast bridge action');

assertSame(true, $runtime->formResponse('uuid-corpus', 1, 0), 'form response is handled');
$actions = $queue->drain();
assertAction($actions, 'player.send_message', 'form response bridge action');

$runtime->syncPlayerInventory('uuid-corpus', [5 => VanillaItems::DIRT()->setCount(3)]);
assertSame('minecraft:dirt', $join->getPlayer()->getInventory()->getItem(5)->getTypeId(), 'host inventory sync updates mirror');

for ($tick = 1; $tick <= 3; $tick++) {
    $runtime->tick($tick);
}

$plugin = $runtime->plugins()[1];
assert($plugin instanceof \Corpus\Runtime\RuntimePlugin);
assertSame(1, $plugin->joins, 'plugin saw one join');
assertSame(1, $plugin->formResponses, 'plugin saw one form response');
assert($plugin->scheduledRuns >= 2, 'plugin scheduler ran');
assert(is_file($plugin->getDataFolder() . 'async-complete.txt'), 'async task completed through pool collection');
assertSame('async-ok', trim((string) file_get_contents($plugin->getDataFolder() . 'async-complete.txt')), 'async completion result persisted');
assertSame(true, $plugin->getConfig()->getNested('runtime.enabled'), 'nested config persisted');
assert($plugin->getServer()->getWorldManager()->isWorldLoaded('corpus'), 'world manager generated local world');

$db = new SQLite3($plugin->getDataFolder() . 'runtime.sqlite');
$row = $db->querySingle('SELECT name, score FROM joins LIMIT 1', true);
assertSame('Alex', $row['name'] ?? null, 'sqlite row persisted player name');
assertSame(28, (int) ($row['score'] ?? 0), 'virion helper value persisted to sqlite');
$db->close();

$runtime->disable();
assert(is_file($plugin->getDataFolder() . 'disabled.txt'), 'disable lifecycle ran');

echo "plugin corpus smoke ok\n";

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL {$label}: expected " . var_export($expected, true) . ', got ' . var_export($actual, true) . "\n");
        exit(1);
    }
}

/** @param list<array<string, mixed>> $actions */
function assertAction(array $actions, string $type, string $label): void
{
    foreach ($actions as $action) {
        if (($action['type'] ?? null) === $type) {
            return;
        }
    }
    fwrite(STDERR, "FAIL {$label}: missing action {$type}; got " . json_encode($actions) . "\n");
    exit(1);
}

function removeTree(string $path): void
{
    if (!is_dir($path)) {
        return;
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($path);
}
