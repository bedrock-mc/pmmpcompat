<?php

declare(strict_types=1);

namespace PmmpCompat\Tests;

use PHPUnit\Framework\TestCase;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\form\SimpleForm;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\permission\Permission;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\World;

final class PocketMineCompatTest extends TestCase
{
    public function testDescriptionParsesPluginYml(): void
    {
        $dir = $this->fixturePlugin();
        $description = PluginDescription::fromFile($dir . '/plugin.yml');

        self::assertSame('FixturePlugin', $description->getName());
        self::assertSame('Fixture\\FixturePlugin', $description->getMain());
        self::assertSame('1.0.0', $description->getVersion());
        self::assertSame(['5.0.0'], $description->getCompatibleApis());
        self::assertArrayHasKey('hello', $description->getCommands());
        self::assertSame(['hi'], $description->getCommands()['hello']['aliases']);
        self::assertArrayHasKey('fixture.use', $description->getPermissions());
    }

    public function testLoaderCallsLifecycleAndRegistersCommands(): void
    {
        $server = new Server();
        $loader = new PluginLoader($server);
        $plugin = $loader->loadFolder($this->fixturePlugin());

        $plugin->__pmmpCallLoad();
        $plugin->__pmmpCallEnable();

        self::assertTrue($plugin->loaded);
        self::assertTrue($plugin->enabled);
        self::assertNotNull($server->getCommandMap()->getCommand('hello'));
        self::assertNotNull($server->getPermissionManager()->getPermission('fixture.use'));

        $sender = new Player('uuid-1', 'Steve');
        self::assertTrue($server->getCommandMap()->dispatch($sender, 'hello', ['world']));
        self::assertSame(['no permission'], $sender->sentMessages());
        $sender->addPermission('fixture.use');
        self::assertTrue($server->getCommandMap()->dispatch($sender, 'hello', ['world']));
        self::assertSame(['no permission', 'hello world'], $sender->sentMessages());

        $plugin->__pmmpCallDisable();
        self::assertTrue($plugin->disabled);
    }

    public function testPluginManagerDispatchesTypedListeners(): void
    {
        $server = new Server();
        $plugin = new class extends PluginBase {
            public bool $joined = false;
            public function enableForTest(Server $server): void
            {
                $this->__pmmpInit($server, new PluginDescription(['name' => 'Test', 'main' => self::class]), sys_get_temp_dir() . '/pmmp-test-data');
                $this->__pmmpCallEnable();
            }
            protected function onEnable(): void
            {
                $this->getServer()->getPluginManager()->registerEvents(new class($this) implements Listener {
                    public function __construct(private object $plugin) {}
                    public function onJoin(PlayerJoinEvent $event): void
                    {
                        $this->plugin->joined = $event->getPlayer()->getName() === 'Alex';
                    }
                    public function onChat(PlayerChatEvent $event): void
                    {
                        $event->setMessage(strtoupper($event->getMessage()));
                    }
                }, $this);
            }
        };

        $plugin->enableForTest($server);
        $player = new Player('uuid-2', 'Alex');
        $join = new PlayerJoinEvent($player);
        $chat = new PlayerChatEvent($player, 'hello');

        $server->getPluginManager()->callEvent($join);
        $server->getPluginManager()->callEvent($chat);

        self::assertTrue($plugin->joined);
        self::assertSame('HELLO', $chat->getMessage());
    }

    public function testSchedulerRunsDelayedAndRepeatingTasks(): void
    {
        $runs = 0;
        $scheduler = (new Server())->getScheduler();
        $scheduler->scheduleDelayedTask(new ClosureTask(static function () use (&$runs): void {
            $runs++;
        }), 2);
        $scheduler->mainThreadHeartbeat(1);
        self::assertSame(0, $runs);
        $scheduler->mainThreadHeartbeat(2);
        self::assertSame(1, $runs);

        $scheduler->scheduleRepeatingTask(new ClosureTask(static function () use (&$runs): void {
            $runs++;
        }), 3);
        $scheduler->mainThreadHeartbeat(3);
        self::assertSame(1, $runs);
        $scheduler->mainThreadHeartbeat(5);
        self::assertSame(2, $runs);
        $scheduler->mainThreadHeartbeat(6);
        self::assertSame(2, $runs);
        $scheduler->mainThreadHeartbeat(8);
        self::assertSame(3, $runs);
    }

    public function testConfigPersistsSimpleValues(): void
    {
        $file = sys_get_temp_dir() . '/pmmpcompat-config-' . getmypid() . '.yml';
        @unlink($file);
        $config = new Config($file, Config::YAML, ['enabled' => true, 'limit' => 7, 'name' => 'demo']);
        $config->save();

        $loaded = new Config($file, Config::YAML);
        self::assertTrue($loaded->get('enabled'));
        self::assertSame(7, $loaded->get('limit'));
        self::assertSame('demo', $loaded->get('name'));
    }

    public function testCommonServerPlayerWorldItemFacades(): void
    {
        $server = new Server();
        $server->getPermissionManager()->addPermission(new Permission('fixture.use'));
        self::assertNotNull($server->getPermissionManager()->getPermission('fixture.use'));

        $player = new Player('uuid-3', 'Casey');
        self::assertFalse($player->hasPermission('fixture.use'));
        $player->addPermission('fixture.use');
        self::assertTrue($player->hasPermission('fixture.use'));

        $player->getInventory()->addItem(VanillaItems::DIAMOND()->setCount(3));
        self::assertSame('minecraft:diamond', $player->getInventory()->getItem(0)?->getTypeId());
        self::assertSame(3, $player->getInventory()->getItem(0)?->getCount());

        $world = new World('world');
        $pos = new Vector3(1, 64, 1);
        $world->setBlock($pos, VanillaBlocks::STONE());
        self::assertSame('minecraft:stone', $world->getBlock($pos)->getTypeId());

        $server->addPlayer($player);
        self::assertSame($player, $server->getPlayerExact('Casey'));
        $server->getConsoleSender()->sendMessage('server message');
        self::assertSame(['server message'], $server->getConsoleSender()->sentMessages());
    }

    public function testFormsAndCommonEvents(): void
    {
        $player = new Player('uuid-4', 'Riley');
        $response = null;
        $form = (new SimpleForm('Menu', 'Pick one', static function (Player $player, mixed $data) use (&$response): void {
            $response = [$player->getName(), $data];
        }))->addButton('Start');

        $player->sendForm($form);
        self::assertSame([1 => $form], $player->sentForms());
        self::assertTrue($player->handleFormResponse(1, 0));
        self::assertSame([], $player->sentForms());
        self::assertSame(['Riley', 0], $response);
        self::assertSame('form', $form->jsonSerialize()['type']);

        $command = new PlayerCommandPreprocessEvent($player, '/hello world');
        $command->setMessage('/hi there');
        self::assertSame('/hi there', $command->getMessage());

        $damage = new EntityDamageByEntityEvent($player, new \stdClass(), EntityDamageEvent::CAUSE_ENTITY_ATTACK, 4.0);
        self::assertSame($player, $damage->getDamager());
        self::assertSame(4.0, $damage->getFinalDamage());
    }

    public function testDirectoryLoadSortsDependencies(): void
    {
        $server = new Server();
        $root = sys_get_temp_dir() . '/pmmpcompat-deps-' . getmypid();
        $this->writeMinimalPlugin($root . '/alpha', 'AlphaPlugin', 'Fixture\\AlphaPlugin', 'loadbefore: [BetaPlugin]');
        $this->writeMinimalPlugin($root . '/beta', 'BetaPlugin', 'Fixture\\BetaPlugin', 'softdepend: [AlphaPlugin]');

        $plugins = (new PluginLoader($server))->loadDirectory($root);

        self::assertSame(['AlphaPlugin', 'BetaPlugin'], array_map(static fn(PluginBase $plugin): string => $plugin->getName(), $plugins));
        self::assertSame('AlphaPlugin', $server->getPluginManager()->getPlugins()[0]->getName());
    }

    private function fixturePlugin(): string
    {
        $dir = sys_get_temp_dir() . '/pmmp-compat-fixture-' . getmypid();
        @mkdir($dir . '/src/Fixture', 0777, true);
        file_put_contents($dir . '/plugin.yml', <<<YAML
name: FixturePlugin
main: Fixture\FixturePlugin
version: 1.0.0
api:
  - 5.0.0
commands:
  hello:
    description: Test command
    aliases:
      - hi
    permission: fixture.use
    permission-message: no permission
permissions:
  fixture.use:
    description: Use fixture command
YAML);
        file_put_contents($dir . '/src/Fixture/FixturePlugin.php', <<<'PHP'
<?php
namespace Fixture;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class FixturePlugin extends PluginBase {
    public bool $loaded = false;
    public bool $enabled = false;
    public bool $disabled = false;

    protected function onLoad(): void { $this->loaded = true; }
    protected function onEnable(): void { $this->enabled = true; }
    protected function onDisable(): void { $this->disabled = true; }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        $sender->sendMessage('hello ' . ($args[0] ?? 'there'));
        return true;
    }
}
PHP);
        return $dir;
    }

    private function writeMinimalPlugin(string $dir, string $name, string $main, string $extraYaml = ''): void
    {
        @mkdir($dir . '/src/Fixture', 0777, true);
        file_put_contents($dir . '/plugin.yml', <<<YAML
name: {$name}
main: {$main}
version: 1.0.0
{$extraYaml}
YAML);
        $class = substr($main, strrpos($main, '\\') + 1);
        file_put_contents($dir . '/src/Fixture/' . $class . '.php', <<<PHP
<?php
namespace Fixture;
class {$class} extends \\pocketmine\\plugin\\PluginBase {}
PHP);
    }
}
