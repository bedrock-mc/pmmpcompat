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
use pocketmine\event\player\PlayerMoveEvent;
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
use pocketmine\world\Position;
use pocketmine\world\World;
use Ramsey\Uuid\UuidInterface;

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
        $path = $this->fixturePlugin();
        $plugin = $loader->loadFolder($path);

        $plugin->__pmmpCallLoad();
        $plugin->__pmmpCallEnable();

        self::assertSame($path, $plugin->getFile());
        self::assertTrue($plugin->loaded);
        self::assertTrue($plugin->enabled);
        self::assertNotNull($server->getCommandMap()->getCommand('hello'));
        self::assertNotNull($server->getPermissionManager()->getPermission('fixture.use'));

        $sender = new Player('00000000-0000-4000-8000-000000000001', 'Steve');
        self::assertTrue($server->getCommandMap()->dispatch($sender, 'hello', ['world']));
        self::assertSame(['no permission'], $sender->sentMessages());
        $sender->addPermission('fixture.use');
        self::assertTrue($server->getCommandMap()->dispatch($sender, 'hello', ['world']));
        self::assertSame(['no permission', 'hello world'], $sender->sentMessages());

        $plugin->__pmmpCallDisable();
        self::assertTrue($plugin->disabled);
    }

    public function testPluginPermissionDefaultTrueAllowsNormalPlayerCommand(): void
    {
        $server = new Server();
        $root = sys_get_temp_dir() . '/pmmpcompat-default-permission-' . getmypid();
        @mkdir($root . '/src/Fixture', 0777, true);
        file_put_contents($root . '/plugin.yml', <<<'YAML'
name: DefaultPermissionPlugin
main: Fixture\DefaultPermissionPlugin
version: 1.0.0
commands:
  open:
    description: Open command
    permission: fixture.open
    permission-message: no permission
permissions:
  fixture.open:
    description: Open fixture command
    default: true
YAML);
        file_put_contents($root . '/src/Fixture/DefaultPermissionPlugin.php', <<<'PHP'
<?php
namespace Fixture;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class DefaultPermissionPlugin extends PluginBase {
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        $sender->sendMessage('open ok');
        return true;
    }
}
PHP);

        $plugin = (new PluginLoader($server))->loadFolder($root);
        $plugin->__pmmpCallEnable();

        $sender = new Player('00000000-0000-4000-8000-000000000006', 'Normal');
        self::assertFalse($sender->isOp());
        self::assertTrue($sender->hasPermission('fixture.open'));
        self::assertTrue($server->getCommandMap()->dispatch($sender, 'open', []));
        self::assertSame(['open ok'], $sender->sentMessages());
    }

    public function testCommandDispatchTreatsVoidExecutionAsHandled(): void
    {
        $server = new Server();
        $server->getCommandMap()->register('fixture', new class('void') extends \pocketmine\command\Command {
            public function execute(\pocketmine\command\CommandSender $sender, string $label, array $args): void
            {
                $sender->sendMessage('void handled');
            }
        });

        $sender = new Player('00000000-0000-4000-8000-000000000007', 'Void');
        self::assertTrue($server->getCommandMap()->dispatch($sender, 'void', []));
        self::assertSame(['void handled'], $sender->sentMessages());
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
        $player = new Player('00000000-0000-4000-8000-000000000002', 'Alex');
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

        $sleeperEntry = (new Server())->getTickSleeper()->addNotifier(static function (): void {});
        self::assertTrue(method_exists($sleeperEntry, 'createNotifier'));
    }

    public function testConfigPersistsSimpleValues(): void
    {
        $file = sys_get_temp_dir() . '/pmmpcompat-config-' . getmypid() . '.yml';
        @unlink($file);
        $config = new Config($file, Config::YAML, [
            'enabled' => true,
            'limit' => 7,
            'name' => 'demo',
            'commands' => ['disabled' => []],
            'aliases' => ['one', 'two'],
        ]);
        $config->save();

        $loaded = new Config($file, Config::YAML);
        self::assertTrue($loaded->get('enabled'));
        self::assertSame(7, $loaded->get('limit'));
        self::assertSame('demo', $loaded->get('name'));
        self::assertSame([], $loaded->getNested('commands.disabled'));
        self::assertSame(['one', 'two'], $loaded->get('aliases'));
    }

    public function testCommonServerPlayerWorldItemFacades(): void
    {
        $server = new Server();
        $server->getPermissionManager()->addPermission(new Permission('fixture.use'));
        self::assertNotNull($server->getPermissionManager()->getPermission('fixture.use'));

        $player = new Player('00000000-0000-4000-8000-000000000003', 'Casey');
        self::assertInstanceOf(UuidInterface::class, $player->getUniqueId());
        self::assertSame('00000000-0000-4000-8000-000000000003', $player->getUniqueId()->toString());
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
        self::assertSame($player, $server->getPlayerByUUID($player->getUniqueId()));
        $server->getConsoleSender()->sendMessage('server message');
        self::assertSame(['server message'], $server->getConsoleSender()->sentMessages());
    }

    public function testFormsAndCommonEvents(): void
    {
        $player = new Player('00000000-0000-4000-8000-000000000004', 'Riley');
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

        $world = new World('arena');
        $from = new Position(1, 64, 1, $world);
        $to = new Position(2, 65, 3, $world);
        $move = new PlayerMoveEvent($player, $from, $to);
        self::assertSame($from, $move->getFrom());
        self::assertSame($to, $move->getTo());
        self::assertSame($world, $move->getTo()->getWorld());

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

    public function testLoaderAcceptsRealWorldManifestAndAutoloadPatterns(): void
    {
        $server = new Server();
        $root = sys_get_temp_dir() . '/pmmpcompat-loader-real-' . getmypid();
        @mkdir($root . '/src/RealWorld/Event', 0777, true);
        @mkdir($root . '/src/RealWorld/Command', 0777, true);
        file_put_contents($root . '/Plugin.yml', <<<'YAML'
name: RealWorldPlugin
main: RealWorld\RealWorldPlugin
version: 1.0.0
commands:
  real:
    description: Real command
YAML);
        file_put_contents($root . '/src/RealWorld/Event/ChildEvent.php', <<<'PHP'
<?php
namespace RealWorld\Event;
class ChildEvent extends ParentEvent {}
PHP);
        file_put_contents($root . '/src/RealWorld/Event/ParentEvent.php', <<<'PHP'
<?php
namespace RealWorld\Event;
class ParentEvent extends \pocketmine\event\Event {}
PHP);
        file_put_contents($root . '/src/RealWorld/Command/RealCommand.php', <<<'PHP'
<?php
namespace RealWorld\Command;
class RealCommand extends \pocketmine\command\Command {
    public function execute(\pocketmine\command\CommandSender $sender, string $commandLabel, array $args) {
        $sender->sendMessage('real');
        return true;
    }
}
PHP);
        file_put_contents($root . '/src/RealWorld/RealWorldPlugin.php', <<<'PHP'
<?php
namespace RealWorld;
class RealWorldPlugin extends \pocketmine\plugin\PluginBase {
    protected function onEnable(): void {
        new \RealWorld\Event\ChildEvent();
        $this->getServer()->getCommandMap()->register('real', new \RealWorld\Command\RealCommand('real'));
    }
}
PHP);

        $plugin = (new PluginLoader($server))->loadFolder($root);
        $plugin->__pmmpCallEnable();

        self::assertSame('RealWorldPlugin', $plugin->getName());
        self::assertNotNull($server->getCommandMap()->getCommand('real'));
    }

    public function testLoaderIncludesPluginVendorAutoload(): void
    {
        $server = new Server();
        $root = sys_get_temp_dir() . '/pmmpcompat-loader-vendor-' . getmypid();
        @mkdir($root . '/src/VendorFixture', 0777, true);
        @mkdir($root . '/vendor/ExternalLib', 0777, true);
        file_put_contents($root . '/plugin.yml', <<<'YAML'
name: VendorFixture
main: VendorFixture\VendorPlugin
version: 1.0.0
YAML);
        file_put_contents($root . '/vendor/autoload.php', <<<'PHP'
<?php
spl_autoload_register(static function(string $class): void{
    if($class === 'ExternalLib\\Thing'){
        require __DIR__ . '/ExternalLib/Thing.php';
    }
});
PHP);
        file_put_contents($root . '/vendor/ExternalLib/Thing.php', <<<'PHP'
<?php
namespace ExternalLib;
final class Thing{
    public static function value(): string{ return 'vendor-ok'; }
}
PHP);
        file_put_contents($root . '/src/VendorFixture/VendorPlugin.php', <<<'PHP'
<?php
namespace VendorFixture;
class VendorPlugin extends \pocketmine\plugin\PluginBase{
    public string $value = '';
    protected function onEnable(): void{
        $this->value = \ExternalLib\Thing::value();
    }
}
PHP);

        $plugin = (new PluginLoader($server))->loadFolder($root);
        $plugin->__pmmpCallEnable();

        self::assertSame('vendor-ok', $plugin->value);
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
