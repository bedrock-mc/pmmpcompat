<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\event\Listener;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerListManager;
use pocketmine\event\RegisteredListener;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockDeathEvent;
use pocketmine\event\block\BlockExplodeEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\block\BlockMeltEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockPreExplodeEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\BlockTeleportEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\BrewItemEvent;
use pocketmine\event\block\BrewingFuelUseEvent;
use pocketmine\event\block\CampfireCookEvent;
use pocketmine\event\block\ChestPairEvent;
use pocketmine\event\block\FarmlandHydrationChangeEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\PressurePlateUpdateEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\StructureGrowEvent;
use pocketmine\event\entity\AreaEffectCloudApplyEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityCombustByBlockEvent;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityExtinguishEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityEffectEvent;
use pocketmine\event\entity\EntityEffectRemoveEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityFrostWalkerEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\entity\ItemDespawnEvent;
use pocketmine\event\entity\ItemMergeEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerDisplayNameChangeEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerEmoteEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerEntityPickEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemEnchantEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\event\player\PlayerPostChunkSendEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnAnchorUseEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\event\player\PlayerViewDistanceChangeEvent;
use pocketmine\form\SimpleForm;
use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\block\Campfire;
use pocketmine\block\BaseSign;
use pocketmine\block\Chest;
use pocketmine\block\Liquid;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\SignText;
use pocketmine\block\tile\BrewingStand;
use pocketmine\block\tile\Furnace;
use pocketmine\color\Color;
use pocketmine\crafting\BrewingRecipe;
use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\CraftingRecipe;
use pocketmine\crafting\RecipeIngredient;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\object\AreaEffectCloud;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\CraftingTransaction;
use pocketmine\inventory\transaction\EnchantingTransaction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\transaction\TransactionBuilder;
use pocketmine\inventory\transaction\TransactionCancelledException;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\inventory\transaction\action\CreateItemAction;
use pocketmine\inventory\transaction\action\DestroyItemAction;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\action\validator\CallbackSlotValidator;
use pocketmine\item\VanillaItems;
use pocketmine\item\enchantment\EnchantingOption;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\IncompatibleEnchantmentRegistry;
use pocketmine\item\enchantment\ItemEnchantmentTags;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\lang\Translatable;
use pocketmine\math\Vector3;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NbtDataException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\chat\LegacyRawChatFormatter;
use pocketmine\player\chat\StandardChatFormatter;
use pocketmine\player\ChunkSelector;
use pocketmine\player\DatFilePlayerDataProvider;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\PlayerDataLoadException;
use pocketmine\player\PlayerDataSaveException;
use pocketmine\player\PlayerInfo;
use pocketmine\player\Player;
use pocketmine\player\SurvivalBlockBreakHandler;
use pocketmine\player\UsedChunkStatus;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ClosureCommand;
use pocketmine\command\FormattedCommandAlias;
use pocketmine\command\PluginCommand;
use pocketmine\command\utils\CommandException;
use pocketmine\command\utils\CommandStringHelper;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\compat\Runtime;
use pocketmine\permission\Permission;
use pocketmine\permission\BanEntry;
use pocketmine\permission\BanList;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionParser;
use pocketmine\permission\PermissionParserException;
use pocketmine\plugin\ApiVersion;
use pocketmine\plugin\DiskResourceProvider;
use pocketmine\plugin\DisablePluginException;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginEnableOrder;
use pocketmine\plugin\PluginGraylist;
use pocketmine\plugin\PluginLoadabilityChecker;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\PluginLoadTriage;
use pocketmine\plugin\PluginLoadTriageEntry;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\plugin\ScriptPluginLoader;
use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\BulkCurlTaskOperation;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\thread\NonThreadSafeValue;
use pocketmine\thread\Thread;
use pocketmine\thread\ThreadCrashException;
use pocketmine\thread\ThreadCrashInfo;
use pocketmine\thread\ThreadCrashInfoFrame;
use pocketmine\thread\ThreadManager;
use pocketmine\thread\log\AttachableThreadSafeLogger;
use pocketmine\thread\log\ThreadSafeLoggerAttachment;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Config;
use pocketmine\utils\Filesystem;
use pocketmine\utils\InternetRequestResult;
use pocketmine\utils\ObjectSet;
use pocketmine\utils\Process;
use pocketmine\utils\Random;
use pocketmine\utils\RegistryTrait;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use pocketmine\utils\VersionString;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\world\Explosion;
use pocketmine\world\BlockTransaction;

$dir = sys_get_temp_dir() . '/pmmpcompat-smoke-' . getmypid();
@mkdir($dir . '/src/Fixture', 0777, true);
@mkdir($dir . '/resources/nested', 0777, true);
file_put_contents($dir . '/plugin.yml', <<<'YAML'
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
file_put_contents($dir . '/resources/config.yml', "enabled: true\nlimit: 11\n");
file_put_contents($dir . '/resources/nested/message.txt', "resource body");
file_put_contents($dir . '/src/Fixture/FixturePlugin.php', <<<'PHP'
<?php
namespace Fixture;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
class FixturePlugin extends PluginBase {
    public bool $loaded = false;
    public bool $enabled = false;
    protected function onLoad(): void { $this->loaded = true; }
    protected function onEnable(): void {
        $this->enabled = true;
        $this->saveDefaultConfig();
        $this->saveResource('nested/message.txt');
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        $sender->sendMessage('hello ' . ($args[0] ?? 'there'));
        return true;
    }
}
PHP);

$server = new Server();
assert(Server::getInstance() instanceof Server);
assert(Server::TARGET_TICKS_PER_SECOND === 20);
assert($server->getName() === 'PocketMine-MP');
assert($server->getApiVersion() === '5.0.0');
assert($server->getMaxPlayers() === Server::DEFAULT_MAX_PLAYERS);
assert($server->getAllowedViewDistance() === Server::DEFAULT_MAX_VIEW_DISTANCE);
assert($server->getTicksPerSecond() === 20.0);
assert($server->getTickUsage() === 0.0);
assert(strlen($server->getServerUniqueId()) === 32);
assert(str_ends_with($server->getDataPath(), DIRECTORY_SEPARATOR));
assert(str_ends_with($server->getPluginPath(), 'plugins' . DIRECTORY_SEPARATOR));
assert($server->requiresAuthentication() === true);
assert($server->isHardcore() === false);
assert($server->isRunning() === true);
$loader = new PluginLoader($server);
$plugin = $loader->loadFolder($dir);
$plugin->__pmmpCallLoad();
$plugin->__pmmpCallEnable();
assert($plugin->loaded === true && $plugin->enabled === true);
assert($plugin->isDisabled() === false);
assert($plugin->getPluginLoader() === $loader);
assert($plugin->getFullName() === 'FixturePlugin v1.0.0');
assert($server->getPluginManager()->isPluginEnabled('FixturePlugin') === true);
assert($plugin->getDescription()->getCompatibleApis() === ['5.0.0']);
assert($plugin->getDescription()->getFullName() === 'FixturePlugin v1.0.0');
assert($plugin->getDescription()->getPrefix() === 'FixturePlugin');
assert($plugin->getDescription()->getOrder()->getAliases() === ['postworld']);
assert($plugin->getDescription()->getMap()['name'] === 'FixturePlugin');
$pluginEnableEvent = new \pocketmine\event\plugin\PluginEnableEvent($plugin);
$pluginDisableEvent = new \pocketmine\event\plugin\PluginDisableEvent($plugin);
assert($pluginEnableEvent->getPlugin() === $plugin && $pluginDisableEvent->getPlugin() === $plugin);
$helloEntry = $plugin->getDescription()->getCommands()['hello'];
assert($helloEntry->getDescription() === 'Test command');
assert($helloEntry->getAliases() === ['hi']);
assert($helloEntry->getPermission() === 'fixture.use');
assert($helloEntry->getPermissionDeniedMessage() === 'no permission');
assert($plugin->getDescription()->getCommandMap()['hello']['description'] === 'Test command');
$graylist = PluginGraylist::fromArray(['mode' => 'blacklist', 'plugins' => ['BlockedPlugin']]);
assert($graylist->isWhitelist() === false);
assert($graylist->isAllowed('FixturePlugin') === true && $graylist->isAllowed('BlockedPlugin') === false);
assert($graylist->toArray()['mode'] === 'blacklist');
$whitelist = new PluginGraylist(['FixturePlugin'], true);
assert($whitelist->isAllowed('FixturePlugin') === true && $whitelist->isAllowed('OtherPlugin') === false);
$triageEntry = new PluginLoadTriageEntry($dir, $loader, $plugin->getDescription());
assert($triageEntry->getFile() === $dir && $triageEntry->getLoader() === $loader);
$triage = new PluginLoadTriage();
$triage->plugins['FixturePlugin'] = $triageEntry;
$triage->dependencies['FixturePlugin'] = ['DependencyPlugin'];
$triage->softDependencies['FixturePlugin'] = ['SoftPlugin'];
assert($triage->plugins['FixturePlugin']->getDescription()->getName() === 'FixturePlugin');
$checker = new PluginLoadabilityChecker($server->getApiVersion());
assert($checker->check($plugin->getDescription()) === null);
$badNameReason = $checker->check(new \pocketmine\plugin\PluginDescription(['name' => 'PocketMineTools', 'version' => '1.0.0', 'api' => ['5.0.0']]));
assert($badNameReason instanceof Translatable && $badNameReason->getText() === 'pocketmine.plugin.restrictedName');
$badApiReason = $checker->check(new \pocketmine\plugin\PluginDescription(['name' => 'BadApi', 'version' => '1.0.0', 'api' => ['6.0.0']]));
assert($badApiReason instanceof Translatable && $badApiReason->getText() === 'pocketmine.plugin.incompatibleAPI');
$badExtensionReason = $checker->check(new \pocketmine\plugin\PluginDescription(['name' => 'BadExt', 'version' => '1.0.0', 'api' => ['5.0.0'], 'extensions' => ['json' => ['definitely-invalid']]]));
assert($badExtensionReason instanceof Translatable && $badExtensionReason->getText() === 'pocketmine.plugin.invalidExtensionVersionConstraint');
$scriptPath = $dir . '/script-plugin.php';
file_put_contents($scriptPath, <<<'PHP'
<?php
/**
 * @name ScriptFixture
 * @main ScriptFixture\ScriptPlugin
 * @version 1.2.3
 * @api 5.0.0
 */
namespace ScriptFixture;
class ScriptPlugin {}
PHP);
$scriptLoader = new ScriptPluginLoader();
assert($scriptLoader->canLoadPlugin($scriptPath) === true);
assert($scriptLoader->getAccessProtocol() === '');
assert($scriptLoader->getPluginDescription($scriptPath)?->getName() === 'ScriptFixture');
$scriptLoader->loadPlugin($scriptPath);
assert(class_exists('ScriptFixture\\ScriptPlugin'));
assert($plugin instanceof Plugin);
assert(ApiVersion::isCompatible('5.1.3', ['5.0.0', '4.0.0']) === true);
assert(ApiVersion::isCompatible('5.1.3', ['6.0.0']) === false);
assert(ApiVersion::checkAmbiguousVersions(['5.0.0', '5.1.0', '5.2.0-alpha']) === ['5.1.0', '5.0.0']);
$resourceProvider = new DiskResourceProvider($dir . '/resources');
$resourceHandle = $resourceProvider->getResource('nested/message.txt');
assert(is_resource($resourceHandle));
assert(stream_get_contents($resourceHandle) === 'resource body');
fclose($resourceHandle);
assert(array_key_exists('nested/message.txt', $resourceProvider->getResources()));
$owned = new class($plugin) {
    use PluginOwnedTrait;
};
assert($owned->getOwningPlugin() === $plugin);
assert(new DisablePluginException('disabled') instanceof RuntimeException);
assert(new AssumptionFailedError('assumption') instanceof Error);
$singletonA = new class {
    use SingletonTrait;
};
$singletonClass = get_class($singletonA);
assert($singletonClass::getInstance() === $singletonClass::getInstance());
$registryClass = new class {
    use RegistryTrait;
    protected static function setup(): void {
        self::_registryRegister('FIRST', new stdClass());
    }
    public static function all(): array {
        return self::_registryGetAll();
    }
};
$registryClassName = get_class($registryClass);
assert($registryClassName::FIRST() instanceof stdClass);
assert(array_key_exists('FIRST', $registryClassName::all()));
assert(TextFormat::colorize('&aGreen &lBold') === TextFormat::GREEN . 'Green ' . TextFormat::BOLD . 'Bold');
assert(TextFormat::clean(TextFormat::RED . 'Red') === 'Red');
assert(TextFormat::javaToBedrock(TextFormat::ESCAPE . 'mstrike') === 'strike');
assert(TextFormat::tokenize(TextFormat::AQUA . 'Hi') === [TextFormat::AQUA, 'Hi']);
assert(TextFormat::addBase(TextFormat::GRAY, 'Hello ' . TextFormat::RESET . 'World') === TextFormat::RESET . TextFormat::GRAY . 'Hello ' . TextFormat::RESET . TextFormat::GRAY . 'World');
assert(TextFormat::toHTML(TextFormat::BOLD . 'Hi') === '<span style="font-weight:bold">Hi</span>');
$objects = new ObjectSet();
$o1 = new stdClass();
$o2 = new stdClass();
$objects->add($o1, $o2);
assert($objects->contains($o1) === true && count($objects->toArray()) === 2);
$objects->remove($o1);
assert($objects->contains($o1) === false && iterator_count($objects->getIterator()) === 1);
$objects->clear();
assert($objects->toArray() === []);
$version = new VersionString('5.1.2-beta', true, 44);
assert($version->getMajor() === 5 && $version->getMinor() === 1 && $version->getPatch() === 2);
assert($version->getSuffix() === 'beta' && $version->getFullVersion(true) === '5.1.2-beta+dev.44');
assert(VersionString::isValidBaseVersion('1.2.3') === true && VersionString::isValidBaseVersion('1.2') === false);
assert($version->compare(new VersionString('5.1.3'), true) === 1);
$randomA = new Random(12345);
$randomB = new Random(12345);
assert($randomA->getSeed() === 12345 && $randomA->nextInt() === $randomB->nextInt());
assert($randomA->nextBoundedInt(10) >= 0 && $randomA->nextBoundedInt(10) < 10);
$requestResult = new InternetRequestResult([['content-type' => 'text/plain']], 'body', 202);
assert($requestResult->getHeaders()[0]['content-type'] === 'text/plain' && $requestResult->getBody() === 'body' && $requestResult->getCode() === 202);
Terminal::init(false);
assert(Terminal::hasFormattingCodes() === false && Terminal::toANSI(TextFormat::GREEN . 'plain') === 'plain');
assert(in_array(Utils::getOS(true), [Utils::OS_WINDOWS, Utils::OS_IOS, Utils::OS_MACOS, Utils::OS_ANDROID, Utils::OS_LINUX, Utils::OS_BSD, Utils::OS_UNKNOWN], true));
assert(Utils::javaStringHash('abc') === 96354);
assert(Utils::printable("a\nb") === 'a.b');
assert(str_contains(Utils::hexdump("A"), '41'));
assert(iterator_to_array(Utils::stringifyKeys([1 => 'one'])) === ['1' => 'one']);
$out = $err = null;
assert(Process::execute(PHP_BINARY . ' -r ' . escapeshellarg('echo "ok";'), $out, $err) === 0 && $out === 'ok');
assert(Process::pid() > 0 && Process::uid() >= 0 && Process::getThreadCount() >= 1);
$fsDir = $dir . '/fs';
@mkdir($fsDir, 0777, true);
Filesystem::safeFilePutContents($fsDir . '/one.txt', 'one');
assert(Filesystem::fileGetContents($fsDir . '/one.txt') === 'one');
Filesystem::addCleanedPath($fsDir, 'clean');
assert(Filesystem::cleanPath($fsDir . '/one.php') === 'clean/one');
Filesystem::recursiveCopy($fsDir, $dir . '/fs-copy');
assert(is_file($dir . '/fs-copy/one.txt'));
$lockPath = $dir . '/runtime.lock';
assert(Filesystem::createLockFile($lockPath) === null);
Filesystem::releaseLockFile($lockPath);
assert(!file_exists($lockPath));
Filesystem::recursiveUnlink($dir . '/fs-copy');
assert(!is_dir($dir . '/fs-copy'));
assert(PluginEnableOrder::fromString('startup')?->getAliases() === ['startup']);
assert($loader->canLoadPlugin($dir) === true);
assert($loader->getPluginDescription($dir)?->getName() === 'FixturePlugin');
assert($loader->getAccessProtocol() === 'file');
assert(VanillaBlocks::getAll()['stone']->getTypeId() === 'minecraft:stone');
assert(VanillaItems::getAll()['diamond']->getTypeId() === 'minecraft:diamond');
$item = VanillaItems::DIAMOND()->setCount(5);
assert((string) $item === 'Diamond (minecraft:diamond) x 5');
assert($item->equals(VanillaItems::DIAMOND()) === true);
assert($item->equalsExact(VanillaItems::DIAMOND()) === false);
$popped = $item->pop(2);
assert($popped->getCount() === 2 && $item->getCount() === 3);
$item->setCustomName('Shiny')->setLore(['one', 'two'])->setCanDestroy(['stone'])->setCanPlaceOn(['dirt'])->setKeepOnDeath();
assert($item->hasCustomName() === true && $item->getCustomName() === 'Shiny');
assert($item->getLore() === ['one', 'two']);
assert($item->getCanDestroy() === ['stone'] && $item->getCanPlaceOn() === ['dirt']);
assert($item->keepOnDeath() === true);
assert(VanillaItems::STONE()->canBePlaced() === true && VanillaItems::STONE()->getBlock()->getTypeId() === 'minecraft:stone');
assert(VanillaItems::AIR()->isNull() === true);
assert(\pocketmine\item\Item::safeNbtDeserialize($item->nbtSerialize())->getTypeId() === 'minecraft:diamond');
$sharpness = VanillaEnchantments::SHARPNESS();
assert($sharpness instanceof Enchantment);
assert($sharpness->getRarity() === Rarity::COMMON && $sharpness->getMaxLevel() === 5);
assert($sharpness->hasPrimaryItemType(ItemFlags::SWORD) === true);
assert($sharpness->getDamageBonus(2) === 1.5);
$instance = new EnchantmentInstance($sharpness, 3);
assert($instance->getType() === $sharpness && $instance->getLevel() === 3);
assert(VanillaEnchantments::getAll()['SHARPNESS'] === $sharpness);
assert(IncompatibleEnchantmentRegistry::getInstance()->areCompatible(VanillaEnchantments::FORTUNE(), VanillaEnchantments::SILK_TOUCH()) === false);
assert(IncompatibleEnchantmentRegistry::getInstance()->areCompatible(VanillaEnchantments::SHARPNESS(), VanillaEnchantments::UNBREAKING()) === true);
assert(ItemEnchantmentTags::SWORD === 'sword');
$block = VanillaBlocks::STONE();
assert((string) $block === 'Stone (minecraft:stone)');
assert($block->getStateId() !== 0);
assert($block->asItem()->getTypeId() === 'minecraft:stone');
assert($block->getDrops(VanillaItems::DIAMOND_SWORD())[0]->getTypeId() === 'minecraft:stone');
assert($block->isSolid() === true && $block->isTransparent() === false);
assert(VanillaBlocks::AIR()->canBeReplaced() === true);
$block->position(new World('block-world'), 1, 2, 3);
assert($block->getPosition()?->x === 1.0);
assert(count(iterator_to_array($block->getAllSides())) === 6);
assert($server->getPermissionManager()->getPermission('fixture.use')?->getDescription() === 'Use fixture command');
$permission = new Permission('fixture.tree', 'Tree permission', ['fixture.child' => true]);
$server->getPermissionManager()->addPermission($permission);
assert($permission->getChildren() === ['fixture.child' => true]);
$permission->addChild('fixture.other', false);
assert($permission->getChildren()['fixture.other'] === false);
$permission->removeChild('fixture.other');
assert(!array_key_exists('fixture.other', $permission->getChildren()));
$permission->setDescription('Changed permission');
assert($permission->getDescription() === 'Changed permission');
$server->getPermissionManager()->addPermission(new Permission('fixture.base', 'Base', ['fixture.child' => true]));
$permissibleBase = new PermissibleBase(['fixture.base' => true]);
assert($permissibleBase->hasPermission('fixture.base') === true);
assert($permissibleBase->hasPermission('fixture.child') === true);
assert($permissibleBase->isPermissionSet('fixture.child') === true);
$callbackDiff = null;
$permissibleBase->getPermissionRecalculationCallbacks()->add(static function (array $diff) use (&$callbackDiff): void { $callbackDiff = $diff; });
$attachment = $permissibleBase->addAttachment($plugin, 'fixture.child', false);
assert($attachment->getPlugin() === $plugin);
assert($permissibleBase->hasPermission('fixture.child') === false);
assert($permissibleBase->getEffectivePermissions()['fixture.child']->getAttachment() === $attachment);
assert($callbackDiff !== null && array_key_exists('fixture.child', $callbackDiff));
$attachment->setPermission('fixture.extra', true);
assert($permissibleBase->hasPermission('fixture.extra') === true);
$attachment->unsetPermission('fixture.extra');
assert($permissibleBase->hasPermission('fixture.extra') === false);
$permissibleBase->removeAttachment($attachment);
assert($permissibleBase->hasPermission('fixture.child') === true);
$recalculated = 0;
$permissible = new class($recalculated) {
    public function __construct(private int &$recalculated) {}
    public function recalculatePermissions(): void { $this->recalculated++; }
};
$server->getPermissionManager()->subscribeToPermission('fixture.tree', $permissible);
assert($server->getPermissionManager()->getPermissionSubscriptions('fixture.tree') === [$permissible]);
$permission->recalculatePermissibles();
assert($recalculated === 1);
$server->getPermissionManager()->unsubscribeFromPermission('fixture.tree', $permissible);
assert($server->getPermissionManager()->getPermissionSubscriptions('fixture.tree') === []);
assert(DefaultPermissionNames::COMMAND_HELP === 'pocketmine.command.help');
assert(DefaultPermissions::ROOT_OPERATOR === DefaultPermissionNames::GROUP_OPERATOR);
$registered = DefaultPermissions::registerPermission(new Permission('fixture.registered', 'Registered'), [$permission]);
assert($registered->getName() === 'fixture.registered');
assert($permission->getChildren()['fixture.registered'] === true);
DefaultPermissions::registerCorePermissions();
assert($server->getPermissionManager()->getPermission(DefaultPermissionNames::COMMAND_HELP) instanceof Permission);
assert(PermissionParser::defaultFromString(true) === PermissionParser::DEFAULT_TRUE);
assert(PermissionParser::defaultFromString('operator') === PermissionParser::DEFAULT_OP);
$permissionGroups = PermissionParser::loadPermissions([
    'fixture.parser' => ['default' => 'true', 'description' => 'Parser permission'],
]);
assert($permissionGroups[PermissionParser::DEFAULT_TRUE][0]->getName() === 'fixture.parser');
try {
    PermissionParser::defaultFromString('bogus');
    assert(false);
} catch (PermissionParserException) {
}
$banEntry = new BanEntry('Steve');
$banEntry->setSource('console');
$banEntry->setReason('testing');
$encodedBan = $banEntry->getString();
$decodedBan = BanEntry::fromString($encodedBan);
assert($decodedBan?->getName() === 'steve' && $decodedBan->getReason() === 'testing');
$banList = new BanList($dir . '/banned-players.txt');
$banList->add($banEntry);
assert($banList->isBanned('Steve') === true);
$banList->setEnabled(false);
assert($banList->isBanned('Steve') === false);
$banList->setEnabled(true);
$banListReloaded = new BanList($dir . '/banned-players.txt');
$banListReloaded->load();
assert($banListReloaded->getEntry('steve')?->getSource() === 'console');
$banListReloaded->remove('Steve');
assert($banListReloaded->isBanned('Steve') === false);
assert($plugin->isEnabled() === true);
$plugin->onEnableStateChange(false);
assert($plugin->isDisabled() === true);
$plugin->onEnableStateChange(true);
assert($plugin->isEnabled() === true);
assert($plugin->getConfig()->get('limit') === 11);
$logger = $plugin->getLogger();
$logger->setFormat('[%s] %s');
assert($logger->getFormat() === '[%s] %s');
$logger->setLogDebug(false);
$beforeDebug = count($logger->records());
$logger->debug('hidden debug');
assert(count($logger->records()) === $beforeDebug);
$logger->debug('forced debug', true);
$logger->alert('alert');
$attached = [];
$attachment = static function (mixed $level, mixed $message) use (&$attached): void {
    $attached[] = [(string) $level, (string) $message];
};
$logger->addAttachment($attachment);
$logger->emergency('emergency');
assert($attached === [['emergency', 'emergency']]);
assert($logger->getAttachments() !== []);
$logger->removeAttachment($attachment);
$logger->buffer(static function () use ($logger): void { $logger->critical('buffered critical'); });
$logger->addAttachment($attachment);
$logger->removeAttachments();
$logger->logException(new RuntimeException('fixture exception'), []);
$logger->syncFlushBuffer();
$logger->shutdownLogWriterThread();
assert($logger->getAttachments() === []);
assert(array_column($logger->records(), 'level') === ['debug', 'alert', 'emergency', 'critical', 'critical']);
assert(is_file($plugin->getDataFolder() . 'nested/message.txt'));
$resource = $plugin->getResource('nested/message.txt');
assert(is_resource($resource));
assert(stream_get_contents($resource) === 'resource body');
fclose($resource);
assert(array_key_exists('nested/message.txt', $plugin->getResources()));

$sender = new Player('00000000-0000-4000-8000-000000000201', 'Steve');
$commandEvent = new \pocketmine\event\server\CommandEvent($sender, 'hello there');
assert($commandEvent->getSender() === $sender && $commandEvent->getCommand() === 'hello there');
$commandEvent->setCommand('changed');
$commandEvent->cancel();
assert($commandEvent->getCommand() === 'changed' && $commandEvent->isCancelled() === true);
$lowMemory = new \pocketmine\event\server\LowMemoryEvent(memory_get_usage(), memory_get_usage(true), false, 2);
assert($lowMemory->getMemory() > 0 && $lowMemory->getMemoryLimit() > 0 && $lowMemory->getTriggerCount() === 2);
assert(is_int($lowMemory->getMemoryFreed()) && $lowMemory->isGlobal() === false);
$networkInterface = new class implements \pocketmine\network\NetworkInterface {
    public string $name = '';
    public function setName(string $name): void { $this->name = $name; }
    public function shutdown(): void {}
    public function start(): void {}
    public function tick(): void {}
};
$networkRegister = new \pocketmine\event\server\NetworkInterfaceRegisterEvent($networkInterface);
assert($networkRegister->getInterface() === $networkInterface);
$networkRegister->cancel();
assert($networkRegister->isCancelled() === true);
assert((new \pocketmine\event\server\NetworkInterfaceUnregisterEvent($networkInterface))->getInterface() === $networkInterface);
$queryInfo = new \pocketmine\network\query\QueryInfo();
assert((new \pocketmine\event\server\QueryRegenerateEvent($queryInfo))->getQueryInfo() === $queryInfo);
$updateChecker = new \pocketmine\updater\UpdateChecker($server);
assert((new \pocketmine\event\server\UpdateNotifyEvent($updateChecker))->getUpdater() === $updateChecker);
$offline = new OfflinePlayer('Steve', [Player::TAG_FIRST_PLAYED => 100, Player::TAG_LAST_PLAYED => 200]);
assert($offline->getName() === 'Steve' && $offline->hasPlayedBefore() === true);
assert($offline->getFirstPlayed() === 100 && $offline->getLastPlayed() === 200);
$playerInfo = new PlayerInfo(TextFormat::RED . 'CleanSteve', 'uuid-info', 'skin-data', 'en_US', ['device' => 'test']);
assert($playerInfo->getUsername() === 'CleanSteve' && $playerInfo->getUuid() === 'uuid-info');
assert($playerInfo->getSkin() === 'skin-data' && $playerInfo->getLocale() === 'en_US');
assert($playerInfo->getExtraData()['device'] === 'test');
$xboxInfo = new XboxLivePlayerInfo('xuid', 'XboxSteve', 'uuid-xbox', 'skin-xbox', 'en_GB', ['x' => 1]);
assert($xboxInfo->getXuid() === 'xuid');
assert($xboxInfo->withoutXboxData() instanceof PlayerInfo && !($xboxInfo->withoutXboxData() instanceof XboxLivePlayerInfo));
assert($sender->getServer() instanceof Server);
assert($sender->getLanguage() === null);
assert($sender->getScreenLineHeight() === 20);
$sender->setScreenLineHeight(7);
assert($sender->getScreenLineHeight() === 7);
$sender->setScreenLineHeight(null);
assert($sender->getScreenLineHeight() === 20);
$sender->sendMessage(new Translatable('chat.type.text'));
assert($sender->sentMessages() === ['chat.type.text']);
$sender->setDisplayName('Display Steve');
assert($sender->getDisplayName() === 'Display Steve');
assert($sender->isOnline() === true && $sender->isAuthenticated() === true);
$sender->setAllowFlight(true);
$sender->setFlying(true);
assert($sender->getAllowFlight() === true && $sender->isFlying() === true);
$sender->toggleFlight(false);
assert($sender->isFlying() === false);
$sender->setAutoJump(false);
$sender->setCanSaveWithChunk(false);
$sender->setHasBlockCollision(false);
assert($sender->hasAutoJump() === false && $sender->canSaveWithChunk() === false && $sender->hasBlockCollision() === false);
$sender->setViewDistance(12);
assert($sender->getViewDistance() === 12);
$sender->setSpawn(new Position(9, 70, 9, new World('spawn-world')));
assert($sender->hasValidCustomSpawn() === true && $sender->getSpawn()?->x === 9.0);
$sender->setDeathPosition(new Position(1, 2, 3, new World('death-world')));
assert($sender->getDeathPosition()?->getWorld()->getFolderName() === 'death-world');
$sender->sendPopup('popup');
$sender->sendTitle('title', 'subtitle');
assert(in_array('popup', $sender->sentMessages(), true));
assert(in_array("title\nsubtitle", $sender->sentMessages(), true));
$command = $plugin->getCommand('hello');
assert($command !== null);
assert($command->getUsage() === '/hello');
assert($command->getLabel() === 'hello');
assert((string) $command === 'hello');
$command->setUsage('/hello <name>');
$command->setDescription('Changed description');
$command->setAliases(['hey']);
assert($command->getUsage() === '/hello <name>');
assert($command->getDescription() === 'Changed description');
assert($command->getAliases() === ['hey']);
assert($command->getExecutor() === $plugin);
assert($server->getCommandMap()->dispatch($sender, 'hi', ['world']) === true);
$messages = $sender->sentMessages();
assert(end($messages) === 'no permission');
$sender->addPermission('fixture.use');
assert($server->getCommandMap()->dispatch($sender, 'hi', ['world']) === true);
$messages = $sender->sentMessages();
assert(end($messages) === 'hello world');
assert($server->getPluginCommand('hello') === $command);
assert($server->getCommandAliases()['hello'] === ['hi']);
$command->setExecutor(new class implements CommandExecutor {
    public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $command, string $label, array $args): bool
    {
        $sender->sendMessage('executor ' . $label);
        return true;
    }
});
assert($server->getCommandMap()->dispatch($sender, 'hi', []) === true);
$messages = $sender->sentMessages();
assert(end($messages) === 'executor hi');
$extra = new PluginCommand('extra', $plugin, ['aliases' => ['x'], 'permission' => 'fixture.use']);
$server->getCommandMap()->registerAll('fixture', [$extra]);
assert($server->getCommandMap()->getCommand('x') === $extra);
assert(array_key_exists('extra', $server->getCommandMap()->getCommands()));
assert($server->getCommandMap()->unregister($extra) === true);
assert($server->getCommandMap()->getCommand('extra') === null);
assert(CommandStringHelper::parseQuoteAware('give "steve jobs" apple \"pie\"') === ['give', 'steve jobs', 'apple', '"pie"']);
$closureRan = null;
$closureCommand = new ClosureCommand('closure', static function ($sender, $command, string $label, array $args) use (&$closureRan): bool {
    $closureRan = [$label, $args];
    $sender->sendMessage('closure ' . implode(',', $args));
    return true;
}, ['fixture.use'], 'Closure command', '/closure <args>', ['cl']);
$server->getCommandMap()->register('fixture', $closureCommand);
assert($server->getCommandMap()->dispatch($sender, 'cl', ['a', 'b']) === true);
assert($closureRan === ['cl', ['a', 'b']]);
$messages = $sender->sentMessages();
assert(end($messages) === 'closure a,b');
$alias = new FormattedCommandAlias('aliashello', ['closure $$1 $2-']);
$server->getCommandMap()->register('fixture', $alias);
assert($server->getCommandMap()->dispatch($sender, 'aliashello', ['required', 'rest', 'args']) === true);
assert($closureRan === ['closure', ['required', 'rest args']]);
assert(new CommandException('command') instanceof RuntimeException);
assert(new InvalidCommandSyntaxException('syntax') instanceof CommandException);
$vanilla = new \pocketmine\command\defaults\HelpCommand();
assert($vanilla->getName() === 'help' && $vanilla->getUsage() === '/help');
assert(\pocketmine\command\defaults\VanillaCommand::MAX_COORD === 30000000);
$server->getCommandMap()->register('pocketmine', $vanilla);
assert($server->getCommandMap()->getCommand('help') === $vanilla);
assert($server->getCommandMap()->dispatch($sender, 'help', []) === true);
$messages = $sender->sentMessages();
assert(end($messages) === '/help is handled by the Dragonfly host in pmmpcompat.');
$defaultCommands = [
    new \pocketmine\command\defaults\BanCommand(),
    new \pocketmine\command\defaults\GamemodeCommand(),
    new \pocketmine\command\defaults\GiveCommand(),
    new \pocketmine\command\defaults\KickCommand(),
    new \pocketmine\command\defaults\TeleportCommand(),
    new \pocketmine\command\defaults\TimeCommand(),
    new \pocketmine\command\defaults\VersionCommand(),
    new \pocketmine\command\defaults\WhitelistCommand(),
    new \pocketmine\command\defaults\XpCommand(),
];
foreach ($defaultCommands as $defaultCommand) {
    assert($defaultCommand instanceof \pocketmine\command\defaults\VanillaCommand);
    assert($defaultCommand->execute($sender, $defaultCommand->getName(), []) === true);
}

$hit = false;
$server->getPluginManager()->registerEvents(new class($hit) implements Listener {
    public function __construct(private bool &$hit) {}
    public function onJoin(PlayerJoinEvent $event): void { $this->hit = $event->getPlayer()->getName() === 'Alex'; }
    public function onChat(PlayerChatEvent $event): void { $event->setMessage(strtoupper($event->getMessage())); }
}, $plugin);
$player = new Player('00000000-0000-4000-8000-000000000202', 'Alex');
$chat = new PlayerChatEvent($player, 'hello');
$server->getPluginManager()->callEvent(new PlayerJoinEvent($player));
$server->getPluginManager()->callEvent($chat);
assert($hit === true && $chat->getMessage() === 'HELLO');
$chat->setPlayer($sender);
assert($chat->getPlayer() === $sender);
$chat->setRecipients([$sender, $server->getConsoleSender()]);
assert($chat->getRecipients() === [$sender, $server->getConsoleSender()]);
$formatter = new LegacyRawChatFormatter('[{%0}] {%1}');
$chat->setFormatter($formatter);
assert($chat->getFormatter() === $formatter);
assert($chat->getFormatter()->format('Steve', 'hello') === '[Steve] hello');
$standardFormatter = new StandardChatFormatter();
$standardFormatted = $standardFormatter->format('Steve', 'hello');
assert($standardFormatted instanceof Translatable && $standardFormatted->getText() === 'chat.type.text');
assert($standardFormatted->getParameter(0) === 'Steve' && $standardFormatted->getParameter(1) === 'hello');
$chat->setCancelled();
assert($chat->isCancelled() === true);
$chat->uncancel();
assert($chat->isCancelled() === false);
$chat->cancel();
assert($chat->isCancelled() === true);
$eventCallHit = false;
$server->getPluginManager()->registerEvents(new class($eventCallHit) implements Listener {
    public function __construct(private bool &$hit) {}
    public function onChat(PlayerChatEvent $event): void { $this->hit = $event->getMessage() === 'DIRECT'; }
}, $plugin);
$direct = new PlayerChatEvent($player, 'DIRECT');
$direct->call();
assert($eventCallHit === true && $direct->getEventName() === 'PlayerChatEvent');
$closureHit = false;
$registration = $server->getPluginManager()->registerEvent(PlayerChatEvent::class, static function (PlayerChatEvent $event) use (&$closureHit): void {
    $closureHit = $event->getMessage() === 'CLOSURE';
}, 0, $plugin);
assert($registration instanceof RegisteredListener);
assert($registration->getPlugin() === $plugin && $registration->getPriority() === EventPriority::MONITOR);
$closureEvent = new PlayerChatEvent($player, 'CLOSURE');
$server->getPluginManager()->callEvent($closureEvent);
assert($closureHit === true);
$priorityOrder = [];
$server->getPluginManager()->registerEvent(PlayerChatEvent::class, static function () use (&$priorityOrder): void { $priorityOrder[] = 'high'; }, EventPriority::HIGH, $plugin);
$server->getPluginManager()->registerEvent(PlayerChatEvent::class, static function () use (&$priorityOrder): void { $priorityOrder[] = 'low'; }, EventPriority::LOW, $plugin);
$server->getPluginManager()->callEvent(new PlayerChatEvent($player, 'ORDER'));
assert($priorityOrder === ['low', 'high']);
$cancelledSeen = [];
$server->getPluginManager()->registerEvent(PlayerChatEvent::class, static function () use (&$cancelledSeen): void { $cancelledSeen[] = 'skip'; }, EventPriority::NORMAL, $plugin);
$server->getPluginManager()->registerEvent(PlayerChatEvent::class, static function () use (&$cancelledSeen): void { $cancelledSeen[] = 'handle'; }, EventPriority::NORMAL, $plugin, true);
$cancelledEvent = new PlayerChatEvent($player, 'CANCELLED');
$cancelledEvent->cancel();
$server->getPluginManager()->callEvent($cancelledEvent);
assert($cancelledSeen === ['handle']);
HandlerListManager::global()->unregisterAll($registration);
$closureHit = false;
$server->getPluginManager()->callEvent(new PlayerChatEvent($player, 'CLOSURE'));
assert($closureHit === false);

$runtime = new Runtime('/path/that/does/not/exist', $server);
assert($server->getPluginPath() === '/path/that/does/not/exist' . DIRECTORY_SEPARATOR);
$join = $runtime->playerJoin('00000000-0000-4000-8000-000000000204', 'Riley');
$runtimeChat = $runtime->chat('00000000-0000-4000-8000-000000000204', 'Riley', 'runtime');
assert($join->getPlayer()->getName() === 'Riley');
assert($runtimeChat->getMessage() === 'RUNTIME');
assert($server->getPlayerByRawUUID('00000000-0000-4000-8000-000000000204')?->getName() === 'Riley');
assert($server->getPlayerByPrefix('Ril')?->getName() === 'Riley');
assert($server->getOfflinePlayer('Riley')?->getName() === 'Riley');
assert($server->getOfflinePlayerData('missing') === null);
assert($server->hasOfflinePlayerData('missing') === false);
$playerDataDir = $dir . '/playerdata';
$playerData = new DatFilePlayerDataProvider($playerDataDir);
$namedTag = CompoundTag::create()
    ->setLong(Player::TAG_FIRST_PLAYED, 1001)
    ->setLong(Player::TAG_LAST_PLAYED, 2002)
    ->setString('displayName', 'Riley')
    ->setIntArray('scores', [1, 2, 3])
    ->setTag('messages', new ListTag([new StringTag('hello')]));
assert($playerData->hasData('RILEY') === false);
$playerData->saveData('RILEY', $namedTag);
assert($playerData->hasData('riley') === true);
$loadedTag = $playerData->loadData('riley');
assert($loadedTag instanceof CompoundTag);
assert($loadedTag->getLong(Player::TAG_FIRST_PLAYED) === 1001);
assert($loadedTag->getString('displayName') === 'Riley');
assert($loadedTag->getIntArray('scores') === [1, 2, 3]);
assert($loadedTag->getTag('messages') instanceof ListTag);
$offlineDataPlayer = new OfflinePlayer('Riley', $loadedTag);
assert($offlineDataPlayer->hasPlayedBefore() === true);
assert($offlineDataPlayer->getFirstPlayed() === 1001 && $offlineDataPlayer->getLastPlayed() === 2002);
$root = (new BigEndianNbtSerializer())->read((new BigEndianNbtSerializer())->write(new TreeRoot($namedTag)));
assert($root->mustGetCompoundTag()->getTag(Player::TAG_FIRST_PLAYED) instanceof LongTag);
assert(new PlayerDataSaveException('save') instanceof RuntimeException);
file_put_contents($playerDataDir . '/bad.dat', 'not gzip');
try {
    $playerData->loadData('bad');
    assert(false);
} catch (PlayerDataLoadException $e) {
    assert(is_file($playerDataDir . '/bad.dat.bak'));
}
try {
    (new BigEndianNbtSerializer())->read('not json');
    assert(false);
} catch (NbtDataException) {
}
assert($server->getTick() === 0);
assert($server->getWorldManager()->getDefaultWorld()?->getFolderName() === 'world');
assert($server->getWorldManager()->generateWorld('smoke-world') === true);
assert($server->getWorldManager()->isWorldLoaded('smoke-world') === true);

$runs = 0;
$delayedTask = new ClosureTask(static function () use (&$runs): void { $runs++; });
$handler = $server->getScheduler()->scheduleDelayedTask($delayedTask, 2);
assert($handler->getDelay() === 2 && $handler->isDelayed() === true);
assert($handler->getOwnerName() === 'server' && $handler->getTaskName() === 'ClosureTask');
assert($server->getScheduler()->isQueued($delayedTask) === true);
$server->getPluginManager()->tickSchedulers(1);
$server->getPluginManager()->tickSchedulers(2);
assert($runs === 1);
assert($server->getTick() === 2);
assert($server->getScheduler()->isQueued($delayedTask) === false);
$server->getScheduler()->setEnabled(false);
$server->getScheduler()->scheduleTask(new ClosureTask(static function () use (&$runs): void { $runs++; }));
$server->getPluginManager()->tickSchedulers(3);
assert($runs === 1);
$server->getScheduler()->shutdown();

$scheduler = new \pocketmine\scheduler\TaskScheduler('compat-test');
$repeatRuns = 0;
$repeatTask = new ClosureTask(static function () use (&$repeatRuns): void {
    $repeatRuns++;
    if ($repeatRuns === 2) {
        throw new CancelTaskException();
    }
});
$repeatHandler = $scheduler->scheduleRepeatingTask($repeatTask, 1);
assert($repeatHandler->isRepeating() === true && $repeatHandler->getPeriod() === 1);
$scheduler->mainThreadHeartbeat(1);
$scheduler->mainThreadHeartbeat(2);
$scheduler->mainThreadHeartbeat(3);
assert($repeatRuns === 2 && $repeatHandler->isCancelled() === true);
$queuedTask = new ClosureTask(static function (): void {});
$scheduler->scheduleDelayedTask($queuedTask, 10);
try {
    $scheduler->scheduleTask($queuedTask);
    assert(false);
} catch (InvalidArgumentException) {
}

$asyncEvents = [];
$asyncTask = new class($asyncEvents) extends AsyncTask {
    public function __construct(private array &$events) {}

    public function onRun(): void
    {
        $this->publishProgress('half');
        $this->setResult(['done' => true]);
    }

    public function onProgressUpdate(mixed $progress): void
    {
        $this->events[] = 'progress:' . $progress;
    }

    public function onCompletion(): void
    {
        $this->events[] = $this->getResult()['done'] === true ? 'complete' : 'bad';
    }
};
$pool = new AsyncPool(2);
$startedWorkers = [];
$hook = static function (int $workerId) use (&$startedWorkers): void { $startedWorkers[] = $workerId; };
$pool->addWorkerStartHook($hook);
$workerId = $pool->submitTask($asyncTask);
assert($workerId === 0 && $asyncTask->isSubmitted() === true);
assert($pool->getTaskQueueSizes() === [0 => 1]);
assert($pool->collectTasks() === false);
assert($asyncTask->isFinished() === true && $asyncTask->hasResult() === true);
assert($asyncEvents === ['progress:half', 'complete']);
assert($pool->getTaskQueueSizes() === [0 => 0]);
assert($startedWorkers === [0]);
try {
    $pool->submitTask($asyncTask);
    assert(false);
} catch (InvalidArgumentException) {
}
$pool->removeWorkerStartHook($hook);
assert($pool->shutdownUnusedWorkers() === 1);
assert($pool->getRunningWorkers() === []);

$bulkOp = new BulkCurlTaskOperation('file://' . $dir . '/bulk.txt', 1.5, ['X-Test: yes'], [19913 => true]);
file_put_contents($dir . '/bulk.txt', 'bulk-ok');
assert($bulkOp->getPage() === 'file://' . $dir . '/bulk.txt');
assert($bulkOp->getTimeout() === 1.5 && $bulkOp->getExtraHeaders() === ['X-Test: yes']);
assert($bulkOp->getExtraOpts() === [19913 => true]);

$promiseResolver = new \pocketmine\promise\PromiseResolver();
$promise = $promiseResolver->getPromise();
$promiseEvents = [];
$promise->onCompletion(
    static function (mixed $result) use (&$promiseEvents): void { $promiseEvents[] = ['success', $result]; },
    static function () use (&$promiseEvents): void { $promiseEvents[] = ['failure']; }
);
assert($promise->isResolved() === false);
$promiseResolver->resolve('done');
assert($promise->isResolved() === true && $promiseEvents === [['success', 'done']]);
$promise->onCompletion(
    static function (mixed $result) use (&$promiseEvents): void { $promiseEvents[] = ['late-success', $result]; },
    static function () use (&$promiseEvents): void { $promiseEvents[] = ['late-failure']; }
);
assert($promiseEvents === [['success', 'done'], ['late-success', 'done']]);
try {
    $promiseResolver->reject();
    assert(false);
} catch (LogicException) {
}
$rejectResolver = new \pocketmine\promise\PromiseResolver();
$rejectEvents = [];
$rejectResolver->getPromise()->onCompletion(
    static function () use (&$rejectEvents): void { $rejectEvents[] = 'success'; },
    static function () use (&$rejectEvents): void { $rejectEvents[] = 'failure'; }
);
$rejectResolver->reject();
assert($rejectResolver->getPromise()->isResolved() === false && $rejectEvents === ['failure']);
try {
    $rejectResolver->resolve('too-late');
    assert(false);
} catch (LogicException) {
}
$emptyAllEvents = [];
\pocketmine\promise\Promise::all([])->onCompletion(
    static function (array $results) use (&$emptyAllEvents): void { $emptyAllEvents = $results; },
    static function (): void { assert(false); }
);
assert($emptyAllEvents === []);
$allA = new \pocketmine\promise\PromiseResolver();
$allB = new \pocketmine\promise\PromiseResolver();
$allEvents = [];
\pocketmine\promise\Promise::all(['a' => $allA->getPromise(), 'b' => $allB->getPromise()])->onCompletion(
    static function (array $results) use (&$allEvents): void { $allEvents[] = $results; },
    static function () use (&$allEvents): void { $allEvents[] = 'rejected'; }
);
$allB->resolve('second');
assert($allEvents === []);
$allA->resolve('first');
assert($allEvents === [['b' => 'second', 'a' => 'first']]);
$allRejectA = new \pocketmine\promise\PromiseResolver();
$allRejectB = new \pocketmine\promise\PromiseResolver();
$allRejectEvents = [];
\pocketmine\promise\Promise::all([$allRejectA->getPromise(), $allRejectB->getPromise()])->onCompletion(
    static function () use (&$allRejectEvents): void { $allRejectEvents[] = 'success'; },
    static function () use (&$allRejectEvents): void { $allRejectEvents[] = 'failure'; }
);
$allRejectB->reject();
$allRejectA->resolve('ignored');
assert($allRejectEvents === ['failure']);

$value = new NonThreadSafeValue(['nested' => ['count' => 1]]);
$copy = $value->deserialize();
$copy['nested']['count'] = 2;
assert($value->deserialize()['nested']['count'] === 1);

$frame = new ThreadCrashInfoFrame('#0 fixture', __FILE__, __LINE__);
assert($frame->getPrintableFrame() === '#0 fixture' && $frame->getFile() === __FILE__);
$crashInfo = ThreadCrashInfo::fromThrowable(new RuntimeException('thread failed'), 'CompatThread');
assert($crashInfo->getType() === RuntimeException::class);
assert($crashInfo->getMessage() === 'thread failed' && $crashInfo->getThreadName() === 'CompatThread');
assert(str_contains($crashInfo->makePrettyMessage(), 'thread failed'));
$crashException = new ThreadCrashException('crashed', $crashInfo);
assert($crashException->getCrashInfo() === $crashInfo);

ThreadManager::init();
$threadRan = false;
$thread = new class($threadRan) extends Thread {
    public function __construct(private bool &$ran) {}

    protected function onRun(): void
    {
        $this->ran = true;
    }
};
assert($thread->start() === true && $threadRan === true);
assert($thread->isStarted() === true && $thread->isTerminated() === true);
assert(ThreadManager::getInstance()->getAll() !== []);
assert($thread->join() === true);
assert(ThreadManager::getInstance()->getAll() === []);
$crashingThread = new class extends Thread {
    protected function onRun(): void
    {
        throw new RuntimeException('boom');
    }
};
assert($crashingThread->start() === false);
assert($crashingThread->getCrashInfo()?->getMessage() === 'boom');
assert(ThreadManager::getInstance()->stopAll() === 0);

$logged = [];
$attachment = new class($logged) extends ThreadSafeLoggerAttachment {
    public function __construct(private array &$logged) {}

    public function log(string $level, string $message): void
    {
        $this->logged[] = [$level, $message];
    }
};
$threadLogger = new class extends AttachableThreadSafeLogger {};
$threadLogger->addAttachment($attachment);
$threadLogger->info('thread-log');
assert($logged === [['info', 'thread-log']]);
assert($threadLogger->getAttachments() !== []);
$threadLogger->removeAttachment($attachment);
assert($threadLogger->getAttachments() === []);

$destructorCalled = false;
$destructorObject = new class($destructorCalled) {
    use \pocketmine\utils\DestructorCallbackTrait;

    public function __construct(private bool &$called)
    {
        $this->getDestructorCallbacks()->add(function (): void {
            $this->called = true;
        });
    }
};
unset($destructorObject);
gc_collect_cycles();
assert($destructorCalled === true);

$parser = new \pocketmine\utils\StringToTParser();
$parser->register('minecraft:stone block', static fn(string $input): string => 'parsed:' . $input);
assert($parser->parse('stone block') === 'parsed:stone block');
$parser->registerAlias('stone block', 'rock');
assert($parser->parse('rock') === 'parsed:rock');
$parser->override('rock', static fn(string $input): string => 'override:' . $input);
assert($parser->parse('rock') === 'override:rock');
assert(in_array('rock', $parser->getKnownAliases(), true));

$queue = new \pocketmine\utils\ReversePriorityQueue();
$queue->insert('slow', 10);
$queue->insert('fast', 1);
assert($queue->extract() === 'fast');

$logThread = new \pocketmine\utils\MainLoggerThread($dir . '/compat.log');
$logThread->write("line-one\n");
$logThread->syncFlushBuffer();
assert(str_contains(file_get_contents($dir . '/compat.log'), 'line-one'));
$logThread->shutdown();

assert(strlen(\pocketmine\utils\Git::getRepositoryStatePretty($dir . '/not-a-repo')) === 40);
\pocketmine\utils\Timezone::init();
assert(\pocketmine\utils\Timezone::get() !== '');
$killer = new \pocketmine\utils\ServerKiller(1);
assert($killer->getThreadName() === 'Server Killer');
$signalHandler = new \pocketmine\utils\SignalHandler(static function (): void {});
$signalHandler->unregister();

class PmmpCompatSmokeEnum
{
    use \pocketmine\utils\EnumTrait;

    protected static function setup(): void
    {
        self::registerAll(new self('ALPHA'), new self('BETA'));
    }
}
assert(PmmpCompatSmokeEnum::ALPHA()->name() === 'ALPHA');
assert(PmmpCompatSmokeEnum::ALPHA()->equals(PmmpCompatSmokeEnum::ALPHA()) === true);
assert(count(PmmpCompatSmokeEnum::getAll()) === 2);

$configPath = $dir . '/config.yml';
$config = new Config($configPath, Config::YAML, ['enabled' => true, 'limit' => 7]);
$config->save();
$loaded = new Config($configPath, Config::YAML);
assert($loaded->get('enabled') === true && $loaded->get('limit') === 7);
$loaded->setNested('database.host', '127.0.0.1');
$loaded->setNested('database.port', 3306);
assert($loaded->getNested('database.host') === '127.0.0.1');
assert($loaded->getNested('database.port') === 3306);
assert($loaded->hasChanged() === true);
assert($loaded->getAll(true) === ['enabled', 'limit', 'database']);
$loaded->feature = 'forms';
assert(isset($loaded->feature) && $loaded->feature === 'forms');
unset($loaded->feature);
assert(!$loaded->exists('feature'));
$loaded->removeNested('database.port');
assert($loaded->getNested('database.port', 'missing') === 'missing');
$loaded->setDefaults(['database' => ['user' => 'root'], 'new-key' => true]);
assert($loaded->getNested('database.user') === 'root' && $loaded->get('new-key') === true);
$loaded->save();
assert($loaded->hasChanged() === false);
$reloadedNested = new Config($configPath, Config::YAML);
assert($reloadedNested->getNested('database.host') === '127.0.0.1');
assert(Config::parseList("one\n\ntwo\n") === ['one', 'two']);
assert(Config::writeList(['one', 'two']) === "one\ntwo");
$properties = Config::parseProperties("enabled=on\nlimit=42\nratio=1.5\nname=demo\n");
assert($properties === ['enabled' => true, 'limit' => 42, 'ratio' => 1.5, 'name' => 'demo']);
assert(str_contains(Config::writeProperties(['enabled' => false]), 'enabled=off'));
$jsonConfig = new Config($dir . '/config.json', Config::JSON, ['name' => 'json']);
$jsonConfig->disableJsonOption(JSON_PRETTY_PRINT);
assert(($jsonConfig->getJsonOptions() & JSON_PRETTY_PRINT) === 0);
$jsonConfig->enableJsonOption(JSON_PRETTY_PRINT);
assert(($jsonConfig->getJsonOptions() & JSON_PRETTY_PRINT) !== 0);
$jsonConfig->setJsonOptions(JSON_UNESCAPED_SLASHES);
assert($jsonConfig->getJsonOptions() === JSON_UNESCAPED_SLASHES);

assert($sender->hasPermission('fixture.use') === true);
$server->addPlayer($sender);
$server->addOp('Steve');
assert($server->isOp('Steve') === true && $sender->isOp() === true);
assert($server->getOps() === ['steve']);
$server->removeOp('Steve');
assert($server->isOp('Steve') === false && $sender->isOp() === false);
$server->addWhitelist('Steve');
assert($server->getWhitelisted() === ['steve']);
assert($server->isWhitelisted('Someone') === true);
$server->removeWhitelist('Steve');
assert($server->getWhitelisted() === []);
$inventory = $sender->getInventory();
assert($inventory->getSize() === 36 && $inventory->getMaxStackSize() === 64);
$inventory->setMaxStackSize(16);
assert($inventory->getMaxStackSize() === 16);
assert($inventory->slotExists(35) === true && $inventory->slotExists(36) === false);
assert($inventory->firstEmpty() === 0 && $inventory->isSlotEmpty(0) === true);
assert($inventory->addItem(VanillaItems::DIAMOND()->setCount(2)) === []);
assert($inventory->getItem(0)->getTypeId() === 'minecraft:diamond');
assert($inventory->contains(VanillaItems::DIAMOND()->setCount(2)) === true);
assert($inventory->first(VanillaItems::DIAMOND()->setCount(1)) === 0);
assert(array_key_exists(0, $inventory->all(VanillaItems::DIAMOND())));
assert($inventory->getAddableItemQuantity(VanillaItems::DIAMOND()) >= 14);
$inventory->setItem(1, VanillaItems::DIAMOND()->setCount(1));
$inventory->swap(0, 1);
assert($inventory->getItem(1)->getCount() === 2);
$leftoverRemoval = $inventory->removeItem(VanillaItems::DIAMOND()->setCount(2));
assert($leftoverRemoval === [] && $inventory->getItem(1)->getCount() === 1);
$inventory->setContents([2 => VanillaItems::DIAMOND()->setCount(3)]);
assert($inventory->getContents()[2]->getCount() === 3);
$inventory->clear(2);
assert($inventory->isSlotEmpty(2) === true);
$inventory->addItem(VanillaItems::DIAMOND()->setCount(1));
$inventory->remove(VanillaItems::DIAMOND());
assert($inventory->first(VanillaItems::DIAMOND()) === -1);
$inventory->onOpen($sender);
assert($inventory->getViewers() === [$sender]);
$openInventoryEvent = new InventoryOpenEvent($inventory, $sender);
assert($openInventoryEvent->getInventory() === $inventory && $openInventoryEvent->getPlayer() === $sender);
assert($openInventoryEvent->getViewers() === [$sender]);
$openInventoryEvent->cancel();
assert($openInventoryEvent->isCancelled() === true);
$inventory->onClose($sender);
assert($inventory->getViewers() === []);
$closeInventoryEvent = new InventoryCloseEvent($inventory, $sender);
assert($closeInventoryEvent->getInventory() === $inventory && $closeInventoryEvent->getPlayer() === $sender);
assert($closeInventoryEvent->getViewers() === []);
$listener = new stdClass();
$inventory->addListener($listener);
assert($inventory->getListeners() === [spl_object_id($listener) => $listener]);
$inventory->clearAll();
assert($inventory->getContents() === []);

$formResponse = null;
$form = (new SimpleForm('Menu', 'Pick', static function (Player $player, mixed $data) use (&$formResponse): void {
    $formResponse = [$player->getName(), $data];
}))->addButton('Start');
$sender->sendForm($form);
assert($sender->sentForms() === [1 => $form]);
assert($sender->handleFormResponse(1, 0) === true);
assert($sender->sentForms() === []);
assert($formResponse === ['Steve', 0]);
assert($form->jsonSerialize()['type'] === 'form');

$preprocess = new PlayerCommandPreprocessEvent($sender, '/hello world');
$preprocess->setMessage('/hi world');
assert($preprocess->getMessage() === '/hi world');
$quit = new PlayerQuitEvent($sender, 'left', 'timeout');
assert($quit->getQuitMessage() === 'left' && $quit->getQuitReason() === 'timeout');
$quit->setQuitMessage(new Translatable('disconnect.left'));
assert($quit->getQuitMessage() instanceof Translatable);
$toggleFlight = new PlayerToggleFlightEvent($sender, true);
assert($toggleFlight->getPlayer() === $sender && $toggleFlight->isFlying() === true);
$toggleFlight->cancel();
assert($toggleFlight->isCancelled() === true);
assert((new PlayerToggleSneakEvent($sender, true))->isSneaking() === true);
assert((new PlayerToggleSprintEvent($sender, false))->isSprinting() === false);
assert((new PlayerToggleSwimEvent($sender, true))->isSwimming() === true);
$gmChange = new PlayerGameModeChangeEvent($sender, \pocketmine\player\GameMode::CREATIVE());
assert($gmChange->getNewGamemode()->equals(\pocketmine\player\GameMode::CREATIVE()));
$xpChange = new PlayerExperienceChangeEvent($sender, 1, 0.25, 2, 0.5);
assert($xpChange->getOldLevel() === 1 && $xpChange->getOldProgress() === 0.25);
assert($xpChange->getNewLevel() === 2 && $xpChange->getNewProgress() === 0.5);
$xpChange->setNewLevel(null);
$xpChange->setNewProgress(1.0);
assert($xpChange->getNewLevel() === null && $xpChange->getNewProgress() === 1.0);
try {
    $xpChange->setNewProgress(1.1);
    assert(false);
} catch (InvalidArgumentException) {
}
$viewDistanceChange = new PlayerViewDistanceChangeEvent($sender, 8, 12);
assert($viewDistanceChange->getOldDistance() === 8 && $viewDistanceChange->getNewDistance() === 12);
$displayNameChange = new PlayerDisplayNameChangeEvent($sender, 'Steve', 'Alex');
assert($displayNameChange->getOldName() === 'Steve' && $displayNameChange->getNewName() === 'Alex');
$loginEvent = new PlayerLoginEvent($sender, 'initial kick');
assert($loginEvent->getPlayer() === $sender && $loginEvent->getKickMessage() === 'initial kick');
$loginEvent->setKickMessage(new Translatable('login.denied'));
$loginEvent->cancel();
assert($loginEvent->isCancelled() === true && $loginEvent->getKickMessage() instanceof Translatable);
$kickEvent = new PlayerKickEvent($sender, 'kicked', 'quit msg', null);
assert($kickEvent->getDisconnectReason() === 'kicked' && $kickEvent->getDisconnectScreenMessage() === 'kicked');
$kickEvent->setDisconnectScreenMessage('screen');
$kickEvent->setDisconnectReason('reason2');
$kickEvent->setQuitMessage('quit2');
assert($kickEvent->getDisconnectReason() === 'reason2' && $kickEvent->getDisconnectScreenMessage() === 'screen' && $kickEvent->getQuitMessage() === 'quit2');
$transferEvent = new PlayerTransferEvent($sender, 'old.example', 19132, 'transfer');
$transferEvent->setAddress('new.example');
$transferEvent->setPort(19133);
$transferEvent->setMessage(new Translatable('transfer.message'));
assert($transferEvent->getAddress() === 'new.example' && $transferEvent->getPort() === 19133 && $transferEvent->getMessage() instanceof Translatable);
$preLoginInfo = new PlayerInfo('PreLogin', 'uuid-pre', null, 'en_US');
$preLogin = new PlayerPreLoginEvent($preLoginInfo, '127.0.0.1', 19132, true);
assert($preLogin->getPlayerInfo() === $preLoginInfo && $preLogin->getIp() === '127.0.0.1' && $preLogin->getPort() === 19132);
assert($preLogin->isAuthRequired() === true && $preLogin->isAllowed() === true);
$preLogin->setAuthRequired(false);
$preLogin->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_BANNED, 'ban', 'ban screen');
$preLogin->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_FULL, 'full');
assert($preLogin->isAuthRequired() === false && $preLogin->isAllowed() === false);
assert($preLogin->isKickFlagSet(PlayerPreLoginEvent::KICK_FLAG_BANNED) === true);
assert($preLogin->getDisconnectReason(PlayerPreLoginEvent::KICK_FLAG_BANNED) === 'ban');
assert($preLogin->getDisconnectScreenMessage(PlayerPreLoginEvent::KICK_FLAG_BANNED) === 'ban screen');
assert($preLogin->getFinalDisconnectReason() === 'full');
$preLogin->clearKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_FULL);
assert($preLogin->getFinalDisconnectReason() === 'ban');
$preLogin->clearAllKickFlags();
assert($preLogin->getKickFlags() === [] && $preLogin->isAllowed() === true);
$heldEvent = new PlayerItemHeldEvent($sender, VanillaItems::DIAMOND(), 3);
$heldItem = $heldEvent->getItem();
assert($heldEvent->getSlot() === 3 && $heldItem->getTypeId() === 'minecraft:diamond');
assert($heldItem !== $heldEvent->getItem());
$heldEvent->cancel();
assert($heldEvent->isCancelled() === true);
$consumeEvent = new PlayerItemConsumeEvent($sender, VanillaItems::DIAMOND());
assert($consumeEvent->getItem()->getTypeId() === 'minecraft:diamond');
$consumeEvent->cancel();
assert($consumeEvent->isCancelled() === true);
assert((new PlayerJumpEvent($sender))->getPlayer() === $sender);
$missSwing = new PlayerMissSwingEvent($sender);
$missSwing->cancel();
assert($missSwing->isCancelled() === true);
$emote = new PlayerEmoteEvent($sender, 'wave');
assert($emote->getEmoteId() === 'wave');
$emote->setEmoteId('clap');
assert($emote->getEmoteId() === 'clap');
$exhaust = new PlayerExhaustEvent($sender, 0.3, PlayerExhaustEvent::CAUSE_SPRINTING);
assert($exhaust->getPlayer() === $sender && $exhaust->getAmount() === 0.3 && $exhaust->getCause() === PlayerExhaustEvent::CAUSE_SPRINTING);
$exhaust->setAmount(0.6);
assert($exhaust->getAmount() === 0.6);
$postChunk = new PlayerPostChunkSendEvent($sender, 4, -2);
assert($postChunk->getChunkX() === 4 && $postChunk->getChunkZ() === -2);
$saveEvent = new PlayerDataSaveEvent(CompoundTag::create()->setString('name', 'Steve'), 'Steve', $sender);
assert($saveEvent->getPlayer() === $sender && $saveEvent->getPlayerName() === 'Steve');
assert($saveEvent->getSaveData()->getString('name') === 'Steve');
$saveEvent->setSaveData(CompoundTag::create()->setString('name', 'Alex'));
assert($saveEvent->getSaveData()->getString('name') === 'Alex');
$saveEvent->cancel();
assert($saveEvent->isCancelled() === true);
$blockPick = new PlayerBlockPickEvent($sender, VanillaBlocks::STONE(), VanillaItems::STONE());
assert($blockPick->getPlayer() === $sender && $blockPick->getBlock()->getTypeId() === 'minecraft:stone');
$blockPickItem = $blockPick->getResultItem();
assert($blockPickItem->getTypeId() === 'minecraft:stone' && $blockPickItem !== $blockPick->getResultItem());
$blockPick->cancel();
assert($blockPick->isCancelled() === true);
$pickedEntity = new Entity();
$entityPick = new PlayerEntityPickEvent($sender, $pickedEntity, VanillaItems::DIAMOND());
assert($entityPick->getEntity() === $pickedEntity && $entityPick->getResultItem()->getTypeId() === 'minecraft:diamond');
$entityPick->cancel();
assert($entityPick->isCancelled() === true);
$respawnWorld = new World('respawn');
$respawn = new PlayerRespawnEvent($sender, new Position(1, 65, 1, $respawnWorld));
assert($respawn->getRespawnPosition()->getWorld() === $respawnWorld);
$respawn->setRespawnPosition(new Position(2, 70, 3, $respawnWorld));
assert($respawn->getRespawnPosition()->equals(new Vector3(2, 70, 3)));
try {
    $respawn->setRespawnPosition(new Position(0, 64, 0, null));
    assert(false);
} catch (InvalidArgumentException) {
}
$anchorUse = new PlayerRespawnAnchorUseEvent($sender, VanillaBlocks::STONE(), PlayerRespawnAnchorUseEvent::ACTION_SET_SPAWN);
assert(PlayerRespawnAnchorUseEvent::ACTION_EXPLODE === 0 && PlayerRespawnAnchorUseEvent::ACTION_SET_SPAWN === 1);
assert($anchorUse->getBlock()->getTypeId() === 'minecraft:stone' && $anchorUse->getAction() === PlayerRespawnAnchorUseEvent::ACTION_SET_SPAWN);
$anchorUse->setAction(PlayerRespawnAnchorUseEvent::ACTION_EXPLODE);
$anchorUse->cancel();
assert($anchorUse->getAction() === PlayerRespawnAnchorUseEvent::ACTION_EXPLODE && $anchorUse->isCancelled() === true);
$bucketFill = new PlayerBucketFillEvent($sender, VanillaBlocks::DIRT(), 1, VanillaItems::AIR(), VanillaItems::STONE());
assert($bucketFill->getPlayer() === $sender && $bucketFill->getBlockClicked()->getTypeId() === 'minecraft:dirt');
assert($bucketFill->getBlockFace() === 1 && $bucketFill->getBucket()->getTypeId() === 'minecraft:air');
assert($bucketFill->getItem()->getTypeId() === 'minecraft:stone');
$bucketFill->setItem(VanillaItems::DIAMOND());
$bucketFill->cancel();
assert($bucketFill->getItem()->getTypeId() === 'minecraft:diamond' && $bucketFill->isCancelled() === true);
$bucketEmpty = new PlayerBucketEmptyEvent($sender, VanillaBlocks::STONE(), 5, VanillaItems::STONE(), VanillaItems::AIR());
assert($bucketEmpty->getBlockFace() === 5 && $bucketEmpty->getBlockClicked()->getTypeId() === 'minecraft:stone');
assert($bucketEmpty->getBucket()->getTypeId() === 'minecraft:stone' && $bucketEmpty->getItem()->isNull());
$furnace = new Furnace();
$furnaceBurn = new FurnaceBurnEvent($furnace, VanillaItems::STONE(), 200);
assert($furnaceBurn->getFurnace() === $furnace && $furnaceBurn->getBlock() === $furnace);
assert($furnaceBurn->getFuel()->getTypeId() === 'minecraft:stone' && $furnaceBurn->getBurnTime() === 200 && $furnaceBurn->isBurning() === true);
$furnaceBurn->setBurnTime(80);
$furnaceBurn->setBurning(false);
$furnaceBurn->cancel();
assert($furnaceBurn->getBurnTime() === 80 && $furnaceBurn->isBurning() === false && $furnaceBurn->isCancelled() === true);
$smeltSource = VanillaItems::DIRT()->setCount(4);
$furnaceSmelt = new FurnaceSmeltEvent($furnace, $smeltSource, VanillaItems::STONE());
assert($furnaceSmelt->getFurnace() === $furnace && $furnaceSmelt->getBlock() === $furnace);
assert($furnaceSmelt->getSource()->getTypeId() === 'minecraft:dirt' && $furnaceSmelt->getSource()->getCount() === 1);
assert($smeltSource->getCount() === 4 && $furnaceSmelt->getResult()->getTypeId() === 'minecraft:stone');
$furnaceSmelt->setResult(VanillaItems::DIAMOND());
$furnaceSmelt->cancel();
assert($furnaceSmelt->getResult()->getTypeId() === 'minecraft:diamond' && $furnaceSmelt->isCancelled() === true);

$txInventory = new Inventory(3);
$txInventory->setItem(0, VanillaItems::STONE(), false);
$slotAction = new SlotChangeAction($txInventory, 0, VanillaItems::STONE(), VanillaItems::DIAMOND());
$transaction = new InventoryTransaction($sender, [$slotAction]);
assert($transaction->getSource() === $sender);
assert($transaction->getActions() === [$slotAction]);
assert($transaction->getInventories() === [$txInventory]);
$transactionEvent = new InventoryTransactionEvent($transaction);
assert($transactionEvent->getTransaction() === $transaction);
$transactionEvent->cancel();
assert($transactionEvent->isCancelled() === true);
$transaction->validate();
$transaction->execute();
assert($transaction->hasExecuted() === true);
assert($txInventory->getItem(0)->getTypeId() === 'minecraft:diamond');
try {
    $transaction->execute();
    assert(false);
} catch (TransactionValidationException) {
}
try {
    new InventoryTransaction($sender, [$slotAction, $slotAction]);
    assert(false);
} catch (InvalidArgumentException) {
}
try {
    (new InventoryTransaction($sender))->validate();
    assert(false);
} catch (TransactionValidationException) {
}
$invalidSlotAction = new SlotChangeAction(new Inventory(1), 8, VanillaItems::AIR(), VanillaItems::STONE());
try {
    (new InventoryTransaction($sender, [$invalidSlotAction]))->validate();
    assert(false);
} catch (TransactionValidationException) {
}
$plainAction = new InventoryAction(VanillaItems::STONE(), VanillaItems::DIRT());
assert($plainAction->getSourceItem()->getTypeId() === 'minecraft:stone');
assert($plainAction->getTargetItem()->getTypeId() === 'minecraft:dirt');
assert($plainAction->onPreExecute($sender) === true);

$craftRecipe = new class implements CraftingRecipe {
    public function getIngredientList(): array { return []; }
    public function getResultsFor(\pocketmine\crafting\CraftingGrid $grid): array { return [VanillaItems::DIAMOND()]; }
    public function matchesCraftingGrid(\pocketmine\crafting\CraftingGrid $grid): bool { return true; }
};
$craftingManager = new CraftingManager();
$craftingTransaction = new CraftingTransaction($sender, $craftingManager, [], $craftRecipe, 2);
$craftInput = VanillaItems::STONE()->setCount(4);
$craftOutput = VanillaItems::DIAMOND()->setCount(2);
$craftEvent = new CraftItemEvent($craftingTransaction, $craftRecipe, 2, [$craftInput], [$craftOutput]);
assert($craftEvent->getTransaction() === $craftingTransaction);
assert($craftEvent->getRecipe() === $craftRecipe);
assert($craftEvent->getRepetitions() === 2);
assert($craftEvent->getPlayer() === $sender);
$craftEventInputs = $craftEvent->getInputs();
$craftEventOutputs = $craftEvent->getOutputs();
assert($craftEventInputs[0]->getTypeId() === 'minecraft:stone' && $craftEventInputs[0]->getCount() === 4);
assert($craftEventOutputs[0]->getTypeId() === 'minecraft:diamond' && $craftEventOutputs[0]->getCount() === 2);
$craftEventInputs[0]->setCount(1);
assert($craftEvent->getInputs()[0]->getCount() === 4);
$craftEvent->cancel();
assert($craftEvent->isCancelled() === true);
$ingredient = new class implements RecipeIngredient {
    public function __toString(): string { return 'stone'; }
    public function accepts(\pocketmine\item\Item $item): bool { return $item->getTypeId() === 'minecraft:stone'; }
};
CraftingTransaction::matchIngredients([VanillaItems::STONE()->setCount(2)], [$ingredient], 2);
try {
    CraftingTransaction::matchIngredients([VanillaItems::DIRT()], [$ingredient], 1);
    assert(false);
} catch (TransactionValidationException) {
}
try {
    (new CraftingTransaction($sender, $craftingManager))->execute();
    assert(false);
} catch (TransactionValidationException) {
}
try {
    $cancelledTx = new InventoryTransaction($sender, [new class(VanillaItems::STONE(), VanillaItems::DIRT()) extends InventoryAction {
        public function onPreExecute(Player $source): bool { return false; }
    }]);
    $cancelledTx->execute();
    assert(false);
} catch (TransactionCancelledException) {
}
$creative = new Player('00000000-0000-4000-8000-000000000203', 'Builder');
$creative->setGamemode(\pocketmine\player\GameMode::CREATIVE());
$creative->getCreativeInventory()->addItem(VanillaItems::DIAMOND());
$createAction = new CreateItemAction(VanillaItems::DIAMOND());
$createAction->validate($creative);
assert($createAction->getSourceItem()->getTypeId() === 'minecraft:diamond' && $createAction->getTargetItem()->isNull());
$destroyAction = new DestroyItemAction(VanillaItems::STONE());
$destroyAction->validate($creative);
assert($destroyAction->getSourceItem()->isNull() && $destroyAction->getTargetItem()->getTypeId() === 'minecraft:stone');
try {
    (new CreateItemAction(VanillaItems::STONE()))->validate($sender);
    assert(false);
} catch (TransactionValidationException) {
}
$dropAction = new DropItemAction(VanillaItems::DIAMOND());
$dropAction->validate($sender);
assert($dropAction->onPreExecute($sender) === true);
try {
    (new DropItemAction(VanillaItems::AIR()))->validate($sender);
    assert(false);
} catch (TransactionValidationException) {
}
$builderInventory = new Inventory(2);
$builderInventory->setItem(0, VanillaItems::STONE(), false);
$builder = new TransactionBuilder();
$wrappedInventory = $builder->getInventory($builderInventory);
assert($wrappedInventory === $builder->getInventory($builderInventory));
assert($wrappedInventory->getActualInventory() === $builderInventory);
assert($wrappedInventory->getItem(0)->getTypeId() === 'minecraft:stone');
$wrappedInventory->setItem(0, VanillaItems::DIAMOND());
$wrappedInventory->setItem(1, VanillaItems::DIRT());
$builderExtraAction = new DropItemAction(VanillaItems::STONE());
$builder->addAction($builderExtraAction);
$builderActions = $builder->generateActions();
assert(count($builderActions) === 3);
assert($builderActions[0] === $builderExtraAction);
assert($builderActions[1] instanceof SlotChangeAction && $builderActions[1]->getSlot() === 0);
assert($builderActions[2] instanceof SlotChangeAction && $builderActions[2]->getSlot() === 1);
assert($wrappedInventory->getContents(true)[0]->getTypeId() === 'minecraft:diamond');
$slotValidator = new CallbackSlotValidator(static fn(Inventory $inventory, \pocketmine\item\Item $item, int $slot): ?TransactionValidationException => $slot === 1 ? new TransactionValidationException('slot blocked') : null);
assert($slotValidator->validate($builderInventory, VanillaItems::STONE(), 0) === null);
assert($slotValidator->validate($builderInventory, VanillaItems::STONE(), 1)?->getMessage() === 'slot blocked');
$enchantOption = new EnchantingOption(3, 'smoke words', [new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1)]);
$enchantTransaction = new EnchantingTransaction($sender, $enchantOption, 3);
$enchantTransaction->addAction(new InventoryAction(VanillaItems::STONE(), VanillaItems::DIAMOND()));
$enchantEvent = new PlayerItemEnchantEvent($sender, $enchantTransaction, $enchantOption, VanillaItems::STONE(), VanillaItems::DIAMOND(), 3);
assert($enchantEvent->getPlayer() === $sender && $enchantEvent->getTransaction() === $enchantTransaction);
assert($enchantEvent->getOption() === $enchantOption && $enchantEvent->getCost() === 3);
assert($enchantEvent->getInputItem()->getTypeId() === 'minecraft:stone' && $enchantEvent->getOutputItem()->getTypeId() === 'minecraft:diamond');
$enchantEvent->cancel();
assert($enchantEvent->isCancelled() === true);
$enchantTransaction->execute();
assert($enchantTransaction->hasExecuted() === true);

$bedEnter = new \pocketmine\event\player\PlayerBedEnterEvent($sender, VanillaBlocks::STONE());
assert($bedEnter->getPlayer() === $sender && $bedEnter->getBed()->getTypeId() === 'minecraft:stone');
$bedEnter->cancel();
assert($bedEnter->isCancelled() === true);
$bedLeave = new \pocketmine\event\player\PlayerBedLeaveEvent($sender, VanillaBlocks::DIRT());
assert($bedLeave->getBed()->getTypeId() === 'minecraft:dirt');
$oldSkin = new \pocketmine\entity\Skin();
$newSkin = new \pocketmine\entity\Skin();
$skinEvent = new \pocketmine\event\player\PlayerChangeSkinEvent($sender, $oldSkin, $newSkin);
assert($skinEvent->getOldSkin() === $oldSkin && $skinEvent->getNewSkin() === $newSkin);
$replacementSkin = new \pocketmine\entity\Skin();
$skinEvent->setNewSkin($replacementSkin);
$skinEvent->cancel();
assert($skinEvent->getNewSkin() === $replacementSkin && $skinEvent->isCancelled() === true);
$creationSession = new \pocketmine\network\mcpe\NetworkSession();
$creationEvent = new \pocketmine\event\player\PlayerCreationEvent($creationSession);
assert($creationEvent->getNetworkSession() === $creationSession);
assert($creationEvent->getBaseClass() === Player::class && $creationEvent->getPlayerClass() === Player::class);
$creationEvent->setBaseClass(Player::class);
$creationEvent->setPlayerClass(Player::class);
$deathEvent = new \pocketmine\event\player\PlayerDeathEvent($sender, [VanillaItems::STONE()], 4, null);
assert($deathEvent->getPlayer() === $sender && $deathEvent->getEntity() === $sender);
assert($deathEvent->getDeathMessage() instanceof Translatable && $deathEvent->getXpDropAmount() === 4);
$deathEvent->setDeathMessage('rip');
$deathEvent->setDeathScreenMessage('screen rip');
$deathEvent->setKeepInventory(true);
$deathEvent->setKeepXp(true);
assert($deathEvent->getDeathMessage() === 'rip' && $deathEvent->getDeathScreenMessage() === 'screen rip');
assert($deathEvent->getKeepInventory() === true && $deathEvent->getKeepXp() === true);
$duplicateLogin = new \pocketmine\event\player\PlayerDuplicateLoginEvent(new \pocketmine\network\mcpe\NetworkSession(), new \pocketmine\network\mcpe\NetworkSession(), 'duplicate', null);
assert($duplicateLogin->getDisconnectReason() === 'duplicate' && $duplicateLogin->getDisconnectScreenMessage() === 'duplicate');
$duplicateLogin->cancel();
assert($duplicateLogin->isCancelled() === true);
$oldBook = new \pocketmine\item\WritableBookBase();
$newBook = new \pocketmine\item\WritableBookBase();
$bookEvent = new \pocketmine\event\player\PlayerEditBookEvent($sender, $oldBook, $newBook, \pocketmine\event\player\PlayerEditBookEvent::ACTION_ADD_PAGE, [1]);
assert($bookEvent->getOldBook() === $oldBook && $bookEvent->getNewBook() === $newBook);
assert($bookEvent->getAction() === \pocketmine\event\player\PlayerEditBookEvent::ACTION_ADD_PAGE && $bookEvent->getModifiedPages() === [1]);
$replacementBook = new \pocketmine\item\WritableBookBase();
$bookEvent->setNewBook($replacementBook);
$bookEvent->cancel();
assert($bookEvent->getNewBook() === $replacementBook && $bookEvent->isCancelled() === true);
$optionsRequest = new \pocketmine\event\player\PlayerEnchantingOptionsRequestEvent($sender, new \pocketmine\block\inventory\EnchantInventory(), [$enchantOption]);
assert($optionsRequest->getInventory() instanceof \pocketmine\block\inventory\EnchantInventory && $optionsRequest->getOptions() === [$enchantOption]);
$optionsRequest->setOptions([]);
$optionsRequest->cancel();
assert($optionsRequest->getOptions() === [] && $optionsRequest->isCancelled() === true);
try {
    $optionsRequest->setOptions([
        new EnchantingOption(1, 'one', []),
        new EnchantingOption(2, 'two', []),
        new EnchantingOption(3, 'three', []),
        new EnchantingOption(4, 'four', []),
    ]);
    assert(false);
} catch (LogicException) {
}
$glideEvent = new \pocketmine\event\player\PlayerToggleGlideEvent($sender, true);
$glideEvent->cancel();
assert($glideEvent->isGliding() === true && $glideEvent->isCancelled() === true);
$pack = new class implements \pocketmine\resourcepacks\ResourcePack {
    public function getPackChunk(int $start, int $length): string { return ''; }
    public function getPackId(): string { return 'pack-1'; }
    public function getPackName(): string { return 'Pack'; }
    public function getPackSize(): int { return 0; }
    public function getPackVersion(): string { return '1.0.0'; }
    public function getSha256(): string { return str_repeat('0', 32); }
};
$resourceOffer = new \pocketmine\event\player\PlayerResourcePackOfferEvent($playerInfo, [], [], false);
$resourceOffer->addResourcePack($pack, 'secret');
assert($resourceOffer->getPlayerInfo() === $playerInfo && $resourceOffer->getResourcePacks()[0] === $pack);
assert($resourceOffer->getEncryptionKeys() === ['pack-1' => 'secret']);
$resourceOffer->setMustAccept(true);
$resourceOffer->setResourcePacks([], []);
assert($resourceOffer->mustAccept() === true && $resourceOffer->getResourcePacks() === []);

$packDir = $dir . '/packs';
@mkdir($packDir, 0777, true);
$packPath = $packDir . '/compat.mcpack';
$packUuid = '123e4567-e89b-12d3-a456-426614174000';
$moduleUuid = '123e4567-e89b-12d3-a456-426614174001';
$zip = new ZipArchive();
assert($zip->open($packPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);
$zip->addFromString('manifest.json', json_encode([
    'format_version' => 2,
    'header' => [
        'name' => 'Compat Pack',
        'description' => 'pmmpcompat',
        'uuid' => $packUuid,
        'version' => [1, 2, 3],
        'min_engine_version' => [1, 20, 0],
    ],
    'modules' => [[
        'type' => 'resources',
        'uuid' => $moduleUuid,
        'version' => [1, 2, 3],
    ]],
    'metadata' => ['authors' => ['pmmpcompat']],
]));
$zip->addFromString('textures/compat.txt', 'hello-pack');
$zip->close();
$zippedPack = new \pocketmine\resourcepacks\ZippedResourcePack($packPath);
assert($zippedPack->getPackName() === 'Compat Pack');
assert($zippedPack->getPackId() === $packUuid && $zippedPack->getPackVersion() === '1.2.3');
assert(strlen($zippedPack->getSha256()) === 32 && $zippedPack->getPackSize() > 0);
assert($zippedPack->getPackChunk(0, 2) !== '');
file_put_contents($packDir . '/resource_packs.yml', "force_resources: true\nresource_stack:\n- compat.mcpack\n");
file_put_contents($packDir . '/compat.mcpack.key', str_repeat('k', 32));
$packManager = new \pocketmine\resourcepacks\ResourcePackManager($packDir, $server->getLogger());
assert($packManager->resourcePacksRequired() === true);
assert($packManager->getResourceStack()[0]->getPackId() === $packUuid);
assert($packManager->getPackById(strtoupper($packUuid))?->getPackName() === 'Compat Pack');
assert($packManager->getPackEncryptionKey($packUuid) === str_repeat('k', 32));
$packManager->setResourcePacksRequired(false);
$packManager->setPackEncryptionKey($packUuid, str_repeat('x', 32));
assert($packManager->resourcePacksRequired() === false && $packManager->getPackEncryptionKey($packUuid) === str_repeat('x', 32));

$entity = new Entity();
$entityInteract = new \pocketmine\event\player\PlayerEntityInteractEvent($sender, $entity, new Vector3(0.5, 1.0, 0.5));
assert($entityInteract->getEntity() === $entity && $entityInteract->getClickPosition()->equals(new Vector3(0.5, 1.0, 0.5)));
$entityInteract->cancel();
assert($entityInteract->isCancelled() === true);
$entitySpawn = new EntitySpawnEvent($entity);
assert($entitySpawn->getEntity() === $entity);
$entityDespawn = new EntityDespawnEvent($entity);
assert($entityDespawn->getEntity() === $entity);
$motion = new EntityMotionEvent($entity, new Vector3(0.1, 0.2, 0.3));
assert($motion->getEntity() === $entity && $motion->getVector()->equals(new Vector3(0.1, 0.2, 0.3)));
$motion->cancel();
assert($motion->isCancelled() === true);
$entityWorld = new World('entity-events');
$chunk = new \pocketmine\world\format\Chunk();
$chunkLoad = new \pocketmine\event\world\ChunkLoadEvent($entityWorld, 4, -2, $chunk, true);
assert($chunkLoad->getWorld() === $entityWorld && $chunkLoad->getChunk() === $chunk);
assert($chunkLoad->getChunkX() === 4 && $chunkLoad->getChunkZ() === -2 && $chunkLoad->isNewChunk() === true);
$chunkPopulate = new \pocketmine\event\world\ChunkPopulateEvent($entityWorld, 4, -2, $chunk);
assert($chunkPopulate->getChunk() === $chunk);
$chunkUnload = new \pocketmine\event\world\ChunkUnloadEvent($entityWorld, 4, -2, $chunk);
$chunkUnload->cancel();
assert($chunkUnload->isCancelled() === true);
$spawnChange = new \pocketmine\event\world\SpawnChangeEvent($entityWorld, new Position(0, 64, 0, $entityWorld));
assert($spawnChange->getPreviousSpawn()->getWorld() === $entityWorld);
$difficultyChange = new \pocketmine\event\world\WorldDifficultyChangeEvent($entityWorld, 1, 2);
assert($difficultyChange->getOldDifficulty() === 1 && $difficultyChange->getNewDifficulty() === 2);
$displayNameChangeWorld = new \pocketmine\event\world\WorldDisplayNameChangeEvent($entityWorld, 'old', 'new');
assert($displayNameChangeWorld->getOldName() === 'old' && $displayNameChangeWorld->getNewName() === 'new');
assert((new \pocketmine\event\world\WorldInitEvent($entityWorld))->getWorld() === $entityWorld);
assert((new \pocketmine\event\world\WorldLoadEvent($entityWorld))->getWorld() === $entityWorld);
assert((new \pocketmine\event\world\WorldSaveEvent($entityWorld))->getWorld() === $entityWorld);
$worldUnload = new \pocketmine\event\world\WorldUnloadEvent($entityWorld);
$worldUnload->cancel();
assert($worldUnload->isCancelled() === true);
$particle = new class implements \pocketmine\world\particle\Particle {
    public function encode(mixed ...$args): mixed { return []; }
};
$particleEvent = new \pocketmine\event\world\WorldParticleEvent($entityWorld, $particle, new Vector3(1, 2, 3), [$sender]);
assert($particleEvent->getParticle() === $particle && $particleEvent->getPosition()->equals(new Vector3(1, 2, 3)));
assert($particleEvent->getRecipients() === [$sender]);
$particle2 = new class implements \pocketmine\world\particle\Particle {
    public function encode(mixed ...$args): mixed { return []; }
};
$particleEvent->setParticle($particle2);
$particleEvent->setRecipients([]);
$particleEvent->cancel();
assert($particleEvent->getParticle() === $particle2 && $particleEvent->getRecipients() === [] && $particleEvent->isCancelled() === true);
$sound = new class implements \pocketmine\world\sound\Sound {
    public function encode(mixed ...$args): mixed { return []; }
};
$soundEvent = new \pocketmine\event\world\WorldSoundEvent($entityWorld, $sound, new Vector3(3, 2, 1), [$sender]);
assert($soundEvent->getSound() === $sound && $soundEvent->getPosition()->equals(new Vector3(3, 2, 1)));
$sound2 = new class implements \pocketmine\world\sound\Sound {
    public function encode(mixed ...$args): mixed { return []; }
};
$soundEvent->setSound($sound2);
$soundEvent->setRecipients([]);
$soundEvent->cancel();
assert($soundEvent->getSound() === $sound2 && $soundEvent->getRecipients() === [] && $soundEvent->isCancelled() === true);
$entityTeleport = new EntityTeleportEvent($entity, new Position(1, 64, 1, $entityWorld), new Position(2, 65, 2, $entityWorld));
assert($entityTeleport->getFrom()->equals(new Vector3(1, 64, 1)) && $entityTeleport->getTo()->equals(new Vector3(2, 65, 2)));
$entityTeleport->setTo(new Position(3, 66, 3, $entityWorld));
$entityTeleport->cancel();
assert($entityTeleport->getTo()->equals(new Vector3(3, 66, 3)) && $entityTeleport->isCancelled() === true);
try {
    $entityTeleport->setTo(new Position(NAN, 1, 1, $entityWorld));
    assert(false);
} catch (InvalidArgumentException) {
}
$entityBlockChange = new EntityBlockChangeEvent($entity, VanillaBlocks::DIRT(), VanillaBlocks::STONE());
assert($entityBlockChange->getBlock()->getTypeId() === 'minecraft:dirt' && $entityBlockChange->getTo()->getTypeId() === 'minecraft:stone');
$entityBlockChange->cancel();
assert($entityBlockChange->isCancelled() === true);
$extinguish = new EntityExtinguishEvent($entity, EntityExtinguishEvent::CAUSE_RAIN);
assert($extinguish->getEntity() === $entity && $extinguish->getCause() === EntityExtinguishEvent::CAUSE_RAIN);
$regain = new EntityRegainHealthEvent($entity, 2.5, EntityRegainHealthEvent::CAUSE_MAGIC);
assert($regain->getAmount() === 2.5 && $regain->getRegainReason() === EntityRegainHealthEvent::CAUSE_MAGIC);
$regain->setAmount(4.0);
$regain->cancel();
assert($regain->getAmount() === 4.0 && $regain->isCancelled() === true);
$projectile = new Projectile();
$projectile->setBaseDamage(3.2);
assert($projectile->getBaseDamage() === 3.2 && $projectile->getResultDamage() === 4);
$launch = new ProjectileLaunchEvent($projectile);
assert($launch->getEntity() === $projectile);
$launch->cancel();
assert($launch->isCancelled() === true);
$trace = new RayTraceResult(new Vector3(1.5, 2.5, 3.5), 1, 'hitbox');
assert($trace->getHitVector()->equals(new Vector3(1.5, 2.5, 3.5)) && $trace->getHitFace() === 1 && $trace->getHitObject() === 'hitbox');
$hitBlock = new ProjectileHitBlockEvent($projectile, $trace, VanillaBlocks::STONE());
assert($hitBlock->getEntity() === $projectile && $hitBlock->getRayTraceResult() === $trace && $hitBlock->getBlockHit()->getTypeId() === 'minecraft:stone');
$hitEntityTarget = new Entity();
$hitEntity = new ProjectileHitEntityEvent($projectile, $trace, $hitEntityTarget);
assert($hitEntity->getEntity() === $projectile && $hitEntity->getEntityHit() === $hitEntityTarget);
$itemEntity = new ItemEntity(VanillaItems::DIAMOND());
assert($itemEntity->getItem()->getTypeId() === 'minecraft:diamond');
$itemEntity->setStackSize(3);
assert($itemEntity->getItem()->getCount() === 3 && $itemEntity->getItem() !== $itemEntity->getItem());
$itemEntity->setOwner('owner-xuid');
$itemEntity->setThrower('thrower-xuid');
$itemEntity->setPickupDelay(10);
$itemEntity->setDespawnDelay(ItemEntity::NEVER_DESPAWN);
assert($itemEntity->getOwner() === 'owner-xuid' && $itemEntity->getThrower() === 'thrower-xuid');
assert($itemEntity->getPickupDelay() === 10 && $itemEntity->getDespawnDelay() === ItemEntity::NEVER_DESPAWN);
try {
    $itemEntity->setDespawnDelay(ItemEntity::MAX_DESPAWN_DELAY + 1);
    assert(false);
} catch (InvalidArgumentException) {
}
$itemSpawn = new ItemSpawnEvent($itemEntity);
assert($itemSpawn->getEntity() === $itemEntity);
$itemDespawn = new ItemDespawnEvent($itemEntity);
$itemDespawn->cancel();
assert($itemDespawn->getEntity() === $itemEntity && $itemDespawn->isCancelled() === true);
$mergeTarget = new ItemEntity(VanillaItems::DIAMOND());
$merge = new ItemMergeEvent($itemEntity, $mergeTarget);
$merge->cancel();
assert($merge->getEntity() === $itemEntity && $merge->getTarget() === $mergeTarget && $merge->isCancelled() === true);
$pickupEntityInventory = new Inventory(9);
$entityPickup = new EntityItemPickupEvent($entity, $itemEntity, VanillaItems::STONE(), $pickupEntityInventory);
$entityPickupItem = $entityPickup->getItem();
assert($entityPickup->getEntity() === $entity && $entityPickup->getOrigin() === $itemEntity);
assert($entityPickup->getInventory() === $pickupEntityInventory && $entityPickupItem->getTypeId() === 'minecraft:stone');
assert($entityPickupItem !== $entityPickup->getItem());
$entityPickup->setItem(VanillaItems::DIRT());
$entityPickup->setInventory(null);
$entityPickup->cancel();
assert($entityPickup->getItem()->getTypeId() === 'minecraft:dirt' && $entityPickup->getInventory() === null && $entityPickup->isCancelled() === true);
$combust = new EntityCombustEvent($entity, 8);
assert($combust->getEntity() === $entity && $combust->getDuration() === 8);
$combust->setDuration(12);
$combust->cancel();
assert($combust->getDuration() === 12 && $combust->isCancelled() === true);
$combustByEntity = new EntityCombustByEntityEvent(new Entity(), $entity, 5);
assert($combustByEntity->getCombuster() instanceof Entity && $combustByEntity->getDuration() === 5);
$combustByBlock = new EntityCombustByBlockEvent(VanillaBlocks::STONE(), $entity, 6);
assert($combustByBlock->getCombuster()->getTypeId() === 'minecraft:stone' && $combustByBlock->getEntity() === $entity);
$preEntityExplode = new EntityPreExplodeEvent($entity, 2.0, 0.0);
assert($preEntityExplode->getRadius() === 2.0 && $preEntityExplode->isIncendiary() === false);
$preEntityExplode->setIncendiary(true);
assert(abs($preEntityExplode->getFireChance() - Explosion::DEFAULT_FIRE_CHANCE) < 0.0001);
$preEntityExplode->setBlockBreaking(false);
$preEntityExplode->setFireChance(0.5);
assert($preEntityExplode->isBlockBreaking() === false && $preEntityExplode->getFireChance() === 0.5);
try {
    new EntityPreExplodeEvent($entity, 0.0);
    assert(false);
} catch (InvalidArgumentException) {
}
$entityExplode = new EntityExplodeEvent($entity, new Position(4, 65, 4, $entityWorld), [VanillaBlocks::DIRT()], 40.0, [VanillaBlocks::STONE()]);
assert($entityExplode->getPosition()->getWorld() === $entityWorld && $entityExplode->getYield() === 40.0);
assert($entityExplode->getBlockList()[0]->getTypeId() === 'minecraft:dirt' && $entityExplode->getIgnitions()[0]->getTypeId() === 'minecraft:stone');
$entityExplode->setYield(80.0);
$entityExplode->setBlockList([VanillaBlocks::STONE()]);
$entityExplode->setIgnitions([]);
$entityExplode->cancel();
assert($entityExplode->getYield() === 80.0 && $entityExplode->getBlockList()[0]->getTypeId() === 'minecraft:stone' && $entityExplode->isCancelled() === true);
try {
    $entityExplode->setYield(-1.0);
    assert(false);
} catch (InvalidArgumentException) {
}
$living = new Living();
$living->setHealth(15.0);
assert($living->getHealth() === 15.0 && $living->getMaxHealth() === 20.0);
$skin = new \pocketmine\entity\Skin('skin-id', str_repeat("\x00", 64 * 32 * 4), '', 'geometry.name', '{"format_version":"1.12.0"}');
assert($skin->getSkinId() === 'skin-id' && strlen($skin->getSkinData()) === 64 * 32 * 4);
assert($skin->getCapeData() === '' && $skin->getGeometryName() === 'geometry.name');
try {
    new \pocketmine\entity\Skin('', '');
    assert(false);
} catch (\pocketmine\entity\InvalidSkinException) {
}
$attribute = new \pocketmine\entity\Attribute(\pocketmine\entity\Attribute::HEALTH, 0.0, 20.0, 10.0);
assert($attribute->getId() === \pocketmine\entity\Attribute::HEALTH && $attribute->getValue() === 10.0);
$attribute->setValue(50.0, true)->setDefaultValue(12.0)->markSynchronized();
assert($attribute->getValue() === 20.0 && $attribute->getDefaultValue() === 12.0 && $attribute->isDesynchronized() === false);
$attribute->setValue(12.0, false, true);
assert($attribute->isDesynchronized() === true);
$attributeMap = new \pocketmine\entity\AttributeMap();
$attributeMap->add($attribute);
assert($attributeMap->get(\pocketmine\entity\Attribute::HEALTH) === $attribute);
assert($attributeMap->needSend()[\pocketmine\entity\Attribute::HEALTH] === $attribute);
$factoryAttribute = \pocketmine\entity\AttributeFactory::getInstance()->mustGet(\pocketmine\entity\Attribute::MOVEMENT_SPEED);
assert($factoryAttribute->getId() === \pocketmine\entity\Attribute::MOVEMENT_SPEED);
$sizeInfo = new \pocketmine\entity\EntitySizeInfo(1.8, 0.6);
assert($sizeInfo->getHeight() === 1.8 && $sizeInfo->getWidth() === 0.6);
assert($sizeInfo->scale(2.0)->getEyeHeight() === $sizeInfo->getEyeHeight() * 2.0);
$location = new \pocketmine\entity\Location(1, 2, 3, new World('loc-world'), 90.0, 45.0);
assert($location->getYaw() === 90.0 && $location->getPitch() === 45.0);
assert(\pocketmine\entity\Location::fromObject(new Vector3(1, 2, 3), $location->getWorld(), 90.0, 45.0)->equals($location));
$animationPayload = (new \pocketmine\entity\animation\ArmSwingAnimation($entity))->encode();
assert($animationPayload['type'] === 'arm_swing' && $animationPayload['entity'] === $entity);
assert((new \pocketmine\entity\animation\ItemEntityStackSizeChangeAnimation($entity, 5))->encode()['new_stack_size'] === 5);
$deathEvent = new EntityDeathEvent($living, [VanillaItems::DIAMOND()], 7);
assert($deathEvent->getEntity() === $living && $deathEvent->getDrops()[0]->getTypeId() === 'minecraft:diamond');
assert($deathEvent->getXpDropAmount() === 7);
$deathEvent->setDrops([VanillaItems::DIRT()]);
$deathEvent->setXpDropAmount(3);
assert($deathEvent->getDrops()[0]->getTypeId() === 'minecraft:dirt' && $deathEvent->getXpDropAmount() === 3);
try {
    $deathEvent->setXpDropAmount(-1);
    assert(false);
} catch (InvalidArgumentException) {
}
$effect = new EffectInstance('speed', 100, 1);
assert($effect->getType() === 'speed' && $effect->getDuration() === 100 && $effect->getEffectLevel() === 2);
$effect->decreaseDuration(40)->setAmplifier(2)->setVisible(false)->setAmbient(true)->setColor(new Color(9, 8, 7));
assert($effect->getDuration() === 60 && $effect->getAmplifier() === 2 && $effect->isVisible() === false && $effect->isAmbient() === true);
assert($effect->getColor()?->toARGB() === 0xff090807);
$speedEffect = (new \pocketmine\entity\effect\StringToEffectParser())->parse('speed');
assert($speedEffect instanceof \pocketmine\entity\effect\SpeedEffect && $speedEffect->getName() === 'Speed');
$effectCollection = new \pocketmine\entity\effect\EffectCollection();
$collectionEffect = new EffectInstance($speedEffect, 5, 0);
assert($effectCollection->add($collectionEffect) === true);
assert($effectCollection->has($speedEffect) === true && $effectCollection->get($speedEffect) === $collectionEffect);
assert($effectCollection->getBubbleColor() instanceof Color);
$manager = new \pocketmine\entity\effect\EffectManager($effectCollection);
$manager->tick(5);
assert($effectCollection->has($speedEffect) === false);
assert((new \pocketmine\entity\effect\InstantHealthEffect())->canTick(new EffectInstance('instant', 1)) === true);
assert((new \pocketmine\entity\effect\PoisonEffect())->canTick(new EffectInstance('poison', 25, 0)) === true);
$effectEvent = new EntityEffectEvent($entity, $effect);
assert($effectEvent->getEntity() === $entity && $effectEvent->getEffect() === $effect);
$effectEvent->cancel();
assert($effectEvent->isCancelled() === true);
$oldEffect = new EffectInstance('speed', 20);
$effectAdd = new EntityEffectAddEvent($entity, $effect, $oldEffect);
assert($effectAdd->willModify() === true && $effectAdd->hasOldEffect() === true && $effectAdd->getOldEffect() === $oldEffect);
$effectRemove = new EntityEffectRemoveEvent($entity, $effect);
$effectRemove->cancel();
assert($effectRemove->isCancelled() === true);
try {
    (new EntityEffectRemoveEvent($entity, new EffectInstance('expired', 0)))->cancel();
    assert(false);
} catch (LogicException) {
}
$liquid = new Liquid();
$liquid->setDecay(3)->setFalling(true)->setStill(false);
assert($liquid->getDecay() === 3 && $liquid->isFalling() === true && $liquid->isStill() === false);
assert($liquid->isSolid() === false && $liquid->canBeFlowedInto() === true);
$cloud = new AreaEffectCloud(new EffectInstance('cloud', 30));
assert($cloud->getRadius() === AreaEffectCloud::DEFAULT_RADIUS && $cloud->getEffects()[0]->getType() === 'cloud');
$cloud->setMaxAge(120);
$cloud->setMaxAgeChangeOnUse(-10);
$cloud->setRadiusChangeOnPickup(-0.1);
$cloud->setRadiusChangeOnUse(-0.2);
$cloud->setRadiusChangePerTick(-0.01);
$cloud->setReapplicationDelay(5);
assert($cloud->getMaxAge() === 120 && $cloud->getMaxAgeChangeOnUse() === -10);
assert($cloud->getRadiusChangeOnPickup() === -0.1 && $cloud->getRadiusChangeOnUse() === -0.2 && $cloud->getRadiusChangePerTick() === -0.01);
assert($cloud->getReapplicationDelay() === 5 && $cloud->isFireProof() === true);
$cloudApply = new AreaEffectCloudApplyEvent($cloud, [$living]);
assert($cloudApply->getEntity() === $cloud && $cloudApply->getAffectedEntities()[0] === $living);
$cloudApply->cancel();
assert($cloudApply->isCancelled() === true);
$shootBow = new EntityShootBowEvent($living, VanillaItems::DIAMOND(), $projectile, 1.2);
assert($shootBow->getEntity() === $living && $shootBow->getBow()->getTypeId() === 'minecraft:diamond');
assert($shootBow->getProjectile() === $projectile && $shootBow->getForce() === 1.2);
$replacementProjectile = new Entity();
$shootBow->setProjectile($replacementProjectile);
$shootBow->setForce(0.7);
$shootBow->cancel();
assert($shootBow->getProjectile() === $replacementProjectile && $shootBow->getForce() === 0.7 && $shootBow->isCancelled() === true);
$frost = new EntityFrostWalkerEvent($living, 2, $liquid, VanillaBlocks::STONE());
assert($frost->getEntity() === $living && $frost->getRadius() === 2 && $frost->getLiquid() === $liquid);
$frost->setRadius(4);
$frost->setLiquid(new Liquid());
$frost->setTargetBlock(VanillaBlocks::DIRT());
$frost->cancel();
assert($frost->getRadius() === 4 && $frost->getTargetBlock()->getTypeId() === 'minecraft:dirt' && $frost->isCancelled() === true);
$damageByBlock = new EntityDamageByBlockEvent(VanillaBlocks::STONE(), $entity, EntityDamageEvent::CAUSE_CONTACT, 6.0, [EntityDamageEvent::MODIFIER_ARMOR => -2.0]);
assert($damageByBlock->getDamager()->getTypeId() === 'minecraft:stone' && $damageByBlock->getFinalDamage() === 4.0);
$child = new Entity();
$damageByChild = new EntityDamageByChildEntityEvent($entity, $child, new Entity(), EntityDamageEvent::CAUSE_PROJECTILE, 5.0);
assert($damageByChild->getDamager() === $entity && $damageByChild->getChild() === $child);
$trample = new EntityTrampleFarmlandEvent($living, VanillaBlocks::DIRT());
$trample->cancel();
assert($trample->getEntity() === $living && $trample->getBlock()->getTypeId() === 'minecraft:dirt' && $trample->isCancelled() === true);

$damage = new EntityDamageByEntityEvent($sender, new stdClass(), EntityDamageEvent::CAUSE_ENTITY_ATTACK, 4.0, [EntityDamageEvent::MODIFIER_ARMOR => -1.0]);
assert($damage->getDamager() === $sender && $damage->getFinalDamage() === 3.0);
assert($damage->getOriginalBaseDamage() === 4.0);
assert($damage->getModifier(EntityDamageEvent::MODIFIER_ARMOR) === -1.0);
$damage->setModifier(2.0, EntityDamageEvent::MODIFIER_STRENGTH);
assert($damage->isApplicable(EntityDamageEvent::MODIFIER_STRENGTH) === true);
assert($damage->getFinalDamage() === 5.0);
$damage->setAttackCooldown(5);
assert($damage->getAttackCooldown() === 5);
$damage->setKnockBack(0.8);
$damage->setVerticalKnockBackLimit(0.9);
assert($damage->getKnockBack() === 0.8 && $damage->getVerticalKnockBackLimit() === 0.9);
assert((new EntityDamageEvent(new stdClass(), 3.0, EntityDamageEvent::CAUSE_MAGIC))->canBeReducedByArmor() === false);

$break = new BlockBreakEvent($sender, new Vector3(1, 2, 3), VanillaItems::DIAMOND_SWORD(), false, [VanillaItems::DIRT()], 4);
assert($break->getItem()->getTypeId() === 'minecraft:diamond_sword');
assert($break->getDrops()[0]->getTypeId() === 'minecraft:dirt');
assert($break->getXpDropAmount() === 4);
$break->setInstaBreak(true);
$break->setXpDropAmount(9);
$break->setDropsVariadic(VanillaItems::DIAMOND());
assert($break->getInstaBreak() === true && $break->getXpDropAmount() === 9 && $break->getDrops()[0]->getTypeId() === 'minecraft:diamond');

$place = new BlockPlaceEvent($sender, new Vector3(4, 5, 6), 'tx', VanillaBlocks::STONE(), VanillaItems::STONE());
assert($place->getTransaction() === 'tx');
assert($place->getBlockAgainst()->getTypeId() === 'minecraft:stone');
assert($place->getItem()->getTypeId() === 'minecraft:stone');
$blockUpdate = new BlockUpdateEvent(VanillaBlocks::DIRT());
assert($blockUpdate->getBlock()->getTypeId() === 'minecraft:dirt');
$blockUpdate->cancel();
assert($blockUpdate->isCancelled() === true);
$grow = new BlockGrowEvent(VanillaBlocks::DIRT(), VanillaBlocks::GRASS(), $sender);
assert($grow->getBlock()->getTypeId() === 'minecraft:dirt' && $grow->getNewState()->getTypeId() === 'minecraft:grass');
assert($grow->getPlayer() === $sender);
$grow->cancel();
assert($grow->isCancelled() === true);
$spread = new BlockSpreadEvent(VanillaBlocks::DIRT(), VanillaBlocks::GRASS(), VanillaBlocks::STONE());
assert($spread->getBlock()->getTypeId() === 'minecraft:dirt');
assert($spread->getSource()->getTypeId() === 'minecraft:grass' && $spread->getNewState()->getTypeId() === 'minecraft:stone');
$melt = new BlockMeltEvent(VanillaBlocks::STONE(), VanillaBlocks::AIR());
assert($melt->getBlock()->getTypeId() === 'minecraft:stone' && $melt->getNewState()->isTransparent());
$burn = new BlockBurnEvent(VanillaBlocks::DIRT(), VanillaBlocks::STONE());
assert($burn->getCausingBlock()->getTypeId() === 'minecraft:stone');
$burn->cancel();
assert($burn->isCancelled() === true);
$leavesDecay = new LeavesDecayEvent(VanillaBlocks::DIRT());
$leavesDecay->cancel();
assert($leavesDecay->isCancelled() === true);
$form = new BlockFormEvent(VanillaBlocks::AIR(), VanillaBlocks::STONE(), VanillaBlocks::DIRT());
assert($form->getBlock()->getTypeId() === 'minecraft:air' && $form->getNewState()->getTypeId() === 'minecraft:stone');
assert($form->getCausingBlock()->getTypeId() === 'minecraft:dirt');
$hydration = new FarmlandHydrationChangeEvent(VanillaBlocks::DIRT(), 1, 3);
assert($hydration->getOldHydration() === 1 && $hydration->getNewHydration() === 3);
$hydration->setNewHydration(7);
assert($hydration->getNewHydration() === 7);
try {
    $hydration->setNewHydration(8);
    assert(false);
} catch (InvalidArgumentException) {
}
$preExplode = new BlockPreExplodeEvent(VanillaBlocks::STONE(), 2.5, $sender, 0.25);
assert($preExplode->getRadius() === 2.5 && $preExplode->getPlayer() === $sender);
assert($preExplode->getFireChance() === 0.25 && $preExplode->isIncendiary() === true);
$preExplode->setRadius(3.0);
$preExplode->setBlockBreaking(false);
$preExplode->setIncendiary(false);
assert($preExplode->getRadius() === 3.0 && $preExplode->isBlockBreaking() === false && $preExplode->getFireChance() === 0.0);
$preExplode->setIncendiary(true);
assert(abs($preExplode->getFireChance() - Explosion::DEFAULT_FIRE_CHANCE) < 0.0001);
try {
    $preExplode->setRadius(0.0);
    assert(false);
} catch (InvalidArgumentException) {
}
try {
    $preExplode->setFireChance(1.1);
    assert(false);
} catch (InvalidArgumentException) {
}
$explosionWorld = new World('explosion');
$explode = new BlockExplodeEvent(VanillaBlocks::STONE(), new Position(1, 65, 2, $explosionWorld), [VanillaBlocks::DIRT()], 50.0, [VanillaBlocks::AIR()]);
assert($explode->getPosition()->getWorld() === $explosionWorld && $explode->getYield() === 50.0);
assert($explode->getAffectedBlocks()[0]->getTypeId() === 'minecraft:dirt' && $explode->getIgnitions()[0]->getTypeId() === 'minecraft:air');
$explode->setYield(75.0);
$explode->setAffectedBlocks([VanillaBlocks::STONE()]);
$explode->setIgnitions([]);
assert($explode->getYield() === 75.0 && $explode->getAffectedBlocks()[0]->getTypeId() === 'minecraft:stone' && $explode->getIgnitions() === []);
try {
    $explode->setAffectedBlocks([new stdClass()]);
    assert(false);
} catch (TypeError) {
}
try {
    $explode->setYield(101.0);
    assert(false);
} catch (InvalidArgumentException) {
}
$pickupInventory = new Inventory(9);
$pickup = new BlockItemPickupEvent(VanillaBlocks::STONE(), new Entity(), VanillaItems::DIAMOND(), $pickupInventory);
$pickupItem = $pickup->getItem();
assert($pickup->getInventory() === $pickupInventory && $pickupItem->getTypeId() === 'minecraft:diamond');
assert($pickupItem !== $pickup->getItem());
$pickup->setItem(VanillaItems::DIRT());
$pickup->setInventory(null);
$pickup->cancel();
assert($pickup->getItem()->getTypeId() === 'minecraft:dirt' && $pickup->getInventory() === null && $pickup->isCancelled() === true);
$death = new BlockDeathEvent(VanillaBlocks::GRASS(), VanillaBlocks::DIRT());
assert($death->getBlock()->getTypeId() === 'minecraft:grass' && $death->getNewState()->getTypeId() === 'minecraft:dirt');
$death->cancel();
assert($death->isCancelled() === true);
$teleport = new BlockTeleportEvent(VanillaBlocks::STONE(), new Vector3(5, 6, 7));
assert($teleport->getTo()->equals(new Vector3(5, 6, 7)));
$teleport->setTo(new Vector3(8, 9, 10));
$teleport->cancel();
assert($teleport->getTo()->equals(new Vector3(8, 9, 10)) && $teleport->isCancelled() === true);
try {
    $teleport->setTo(new Vector3(INF, 0, 0));
    assert(false);
} catch (InvalidArgumentException) {
}
$leftChest = new Chest();
$rightChest = new Chest();
$chestPair = new ChestPairEvent($leftChest, $rightChest);
assert($chestPair->getLeft() === $leftChest && $chestPair->getRight() === $rightChest);
$chestPair->cancel();
assert($chestPair->isCancelled() === true);
$pressureEntity = new Entity();
$pressure = new PressurePlateUpdateEvent(VanillaBlocks::STONE(), VanillaBlocks::DIRT(), [$pressureEntity]);
assert($pressure->getBlock()->getTypeId() === 'minecraft:stone' && $pressure->getNewState()->getTypeId() === 'minecraft:dirt');
assert($pressure->getActivatingEntities()[0] === $pressureEntity);
$pressure->cancel();
assert($pressure->isCancelled() === true);
$brewingStand = new BrewingStand();
$brewingRecipe = new class implements BrewingRecipe {
    public function getResultFor(\pocketmine\item\Item $input): ?\pocketmine\item\Item { return VanillaItems::DIAMOND(); }
};
$brew = new BrewItemEvent($brewingStand, 2, VanillaItems::AIR(), VanillaItems::STONE(), $brewingRecipe);
assert($brew->getBrewingStand() === $brewingStand && $brew->getBlock() === $brewingStand);
assert($brew->getSlot() === 2 && $brew->getInput()->isNull() && $brew->getResult()->getTypeId() === 'minecraft:stone');
assert($brew->getInput() !== $brew->getInput() && $brew->getResult() !== $brew->getResult());
$brew->setResult(VanillaItems::DIAMOND());
$brew->cancel();
assert($brew->getResult()->getTypeId() === 'minecraft:diamond' && $brew->getRecipe() === $brewingRecipe && $brew->isCancelled() === true);
$fuel = new BrewingFuelUseEvent($brewingStand);
assert($fuel->getFuelTime() === 20 && $fuel->getBrewingStand() === $brewingStand);
$fuel->setFuelTime(4);
$fuel->cancel();
assert($fuel->getFuelTime() === 4 && $fuel->isCancelled() === true);
try {
    $fuel->setFuelTime(0);
    assert(false);
} catch (InvalidArgumentException) {
}
$campfire = new Campfire();
$campfire->setCookingTime(120);
assert($campfire->getCookingTime() === 120 && $campfire->getInventory()->getSize() === 4);
$cook = new CampfireCookEvent($campfire, 1, VanillaItems::DIRT(), VanillaItems::STONE());
assert($cook->getCampfire() === $campfire && $cook->getSlot() === 1);
assert($cook->getInput()->getTypeId() === 'minecraft:dirt' && $cook->getResult()->getTypeId() === 'minecraft:stone');
$cook->setResult(VanillaItems::DIAMOND());
$cook->cancel();
assert($cook->getResult()->getTypeId() === 'minecraft:diamond' && $cook->isCancelled() === true);
$txWorld = new World('tx');
$tx = new BlockTransaction($txWorld);
$tx->addBlockAt(1, 65, 1, VanillaBlocks::DIRT());
assert($tx->fetchBlockAt(1, 65, 1)->getTypeId() === 'minecraft:dirt');
assert(iterator_to_array($tx->getBlocks())[0] === [1, 65, 1, $tx->fetchBlockAt(1, 65, 1)]);
$structure = new StructureGrowEvent(VanillaBlocks::DIRT(), $tx, $sender);
assert($structure->getTransaction() === $tx && $structure->getPlayer() === $sender);
$structure->cancel();
assert($structure->isCancelled() === true);
assert($tx->apply() === true && $txWorld->getBlockAt(1, 65, 1)->getTypeId() === 'minecraft:dirt');
$black = new Color(0, 0, 0);
assert($black->toARGB() === 0xff000000);
$signText = new SignText(['a', 'b'], new Color(1, 2, 3), true);
assert(SignText::LINE_COUNT === 4);
assert($signText->getLine(0) === 'a' && $signText->getLine(1) === 'b' && $signText->getLine(2) === '');
assert($signText->getBaseColor()->toARGB() === 0xff010203 && $signText->isGlowing() === true);
$blobText = SignText::fromBlob("x\ny\nz\nw\nignored");
assert($blobText->getLines() === ['x', 'y', 'z', 'w']);
try {
    new SignText(['ok', "bad\nline"]);
    assert(false);
} catch (InvalidArgumentException) {
}
try {
    $signText->getLine(4);
    assert(false);
} catch (InvalidArgumentException) {
}
$sign = new BaseSign();
$sign->setFaceText(true, new SignText(['old']));
$sign->setFaceText(false, new SignText(['back']));
$signEvent = new SignChangeEvent($sign, $sender, new SignText(['new']), false);
assert($signEvent->getSign() === $sign && $signEvent->getPlayer() === $sender);
assert($signEvent->getOldText()->getLine(0) === 'back' && $signEvent->getNewText()->getLine(0) === 'new');
assert($signEvent->isFrontFace() === false && $signEvent->getBlock() === $sign);
$signEvent->setNewText(new SignText(['changed']));
$signEvent->cancel();
assert($signEvent->getNewText()->getLine(0) === 'changed' && $signEvent->isCancelled() === true);
assert($sign->isSolid() === false && $sign->getMaxStackSize() === 16 && $sign->getFuelTime() === 200);
$sign->setWaxed(true);
assert($sign->isWaxed() === true);
assert($sign->updateFaceText($sender, true, new SignText(['waxed'])) === false);
$sign->setWaxed(false);
assert($sign->updateFaceText($sender, true, new SignText(["\u{00a7}cClean"])) === true);
assert($sign->getFaceText(true)->getLine(0) === 'cClean');

$interact = new PlayerInteractEvent($sender, VanillaItems::STONE(), VanillaBlocks::DIRT(), new Vector3(0.5, 1, 0.5), PlayerInteractEvent::RIGHT_CLICK_BLOCK, 2);
assert($interact->getFace() === 2 && $interact->useItem() === true && $interact->useBlock() === true);
$interact->setUseItem(false);
$interact->setUseBlock(false);
assert($interact->useItem() === false && $interact->useBlock() === false);
$itemUse = new PlayerItemUseEvent($sender, VanillaItems::DIAMOND(), new Vector3(0, 1, 0));
assert($itemUse->getDirectionVector()->y === 1.0);

$positionWorld = new World('runtime-world');
$position = new Position(1, 2, 3, $positionWorld);
assert($position->isValid() === true);
assert($position->asPosition()->equals($position) === true);
assert(Position::fromObject(new Vector3(1, 2, 3), $positionWorld)->equals($position) === true);
assert($position->distanceSquared(new Vector3(2, 2, 3)) === 1.0);
assert($position->getSide(1)->y === 3.0);
assert((string) $position === 'Position(world=runtime-world,x=1,y=2,z=3)');
$positionWorld->setBlockAt(2, 3, 4, VanillaBlocks::DIRT());
assert($positionWorld->getBlockAt(2, 3, 4)->getTypeId() === 'minecraft:dirt');
$positionWorld->setTime(World::TIME_NIGHT);
assert($positionWorld->getTime() === World::TIME_NIGHT);
assert($positionWorld->getSunAngleDegrees() > 0);
$positionWorld->setDifficulty(World::DIFFICULTY_HARD);
assert($positionWorld->getDifficulty() === World::DIFFICULTY_HARD);
$positionWorld->setAutoSave(false);
assert($positionWorld->getAutoSave() === false);
assert($positionWorld->isLoaded() === true && $positionWorld->isInWorld(0, 64, 0) === true);
assert($positionWorld->getSafeSpawn()->getWorld() === $positionWorld);
assert($positionWorld->getBlockLightAt(0, 0, 0) === 0 && $positionWorld->getFullLightAt(0, 0, 0) === 15);
assert($positionWorld->isChunkLoaded(0, 0) === true && $positionWorld->loadChunk(0, 0) === true);
assert($positionWorld->getPlayers() === []);
assert(UsedChunkStatus::NEEDED()->equals(UsedChunkStatus::NEEDED) === true);
assert(UsedChunkStatus::SENT()->name() === 'SENT');
assert(array_keys(UsedChunkStatus::getAll()) === ['NEEDED', 'REQUESTED_GENERATION', 'REQUESTED_SENDING', 'SENT']);
$selectedChunks = iterator_to_array((new ChunkSelector())->selectChunks(1, 0, 0), false);
sort($selectedChunks);
assert($selectedChunks === ['-1:-1', '-1:0', '0:-1', '0:0']);
assert($sender->getUsedChunkStatus(0, 0) === null);
assert($sender->hasReceivedChunk(0, 0) === false && $sender->isUsingChunk(0, 0) === false);
$breakHandler = new SurvivalBlockBreakHandler($sender, new Vector3(0, 0, 0), VanillaBlocks::STONE(), 1, 16);
assert($breakHandler->getBlockPos()->equals(new Vector3(0, 0, 0)));
assert($breakHandler->getTargetedFace() === 1);
$breakHandler->setTargetedFace(2);
assert($breakHandler->getTargetedFace() === 2);
assert($breakHandler->getBreakSpeed() > 0.0);
$stillBreaking = true;
for ($i = 0; $i < 40 && $stillBreaking; $i++) {
    $stillBreaking = $breakHandler->update();
}
assert($stillBreaking === false && $breakHandler->getBreakProgress() >= 1.0);

$clickSound = new \pocketmine\world\sound\ClickSound(0.75);
assert($clickSound instanceof \pocketmine\world\sound\Sound);
assert($clickSound->getPitch() === 0.75);
$clickPayload = $clickSound->encode(new Vector3(1, 2, 3));
assert($clickPayload['kind'] === 'sound' && $clickPayload['name'] === 'ClickSound');
assert($clickPayload['constructorArgs'] === [0.75]);
assert($clickPayload['encodeArgs'][0] instanceof Vector3);
$noteSound = new \pocketmine\world\sound\NoteSound(\pocketmine\world\sound\NoteInstrument::BANJO(), 12);
assert($noteSound instanceof \pocketmine\world\sound\Sound);
assert($noteSound->encode()['name'] === 'NoteSound');
assert(\pocketmine\world\sound\NoteInstrument::BANJO()->equals(\pocketmine\world\sound\NoteInstrument::BANJO) === true);
$blockUseSound = new \pocketmine\world\sound\ItemUseOnBlockSound(VanillaBlocks::STONE());
assert($blockUseSound->getBlock()->getTypeId() === 'minecraft:stone');
$levelSound = new \pocketmine\world\sound\XpLevelUpSound(17);
assert($levelSound->getXpLevel() === 17);

$flameParticle = new \pocketmine\world\particle\FlameParticle();
assert($flameParticle instanceof \pocketmine\world\particle\Particle);
$flamePayload = $flameParticle->encode(new Vector3(4, 5, 6));
assert($flamePayload['kind'] === 'particle' && $flamePayload['name'] === 'FlameParticle');
assert($flamePayload['encodeArgs'][0] instanceof Vector3);
$dustParticle = new \pocketmine\world\particle\DustParticle(new Color(1, 2, 3));
assert($dustParticle->encode()['constructorArgs'][0] instanceof Color);
$floatingText = new \pocketmine\world\particle\FloatingTextParticle('body', 'title');
assert($floatingText->getText() === 'body' && $floatingText->getTitle() === 'title');
assert($floatingText->isInvisible() === false);
$floatingText->setText('changed');
$floatingText->setTitle('heading');
$floatingText->setInvisible();
assert($floatingText->getText() === 'changed' && $floatingText->getTitle() === 'heading' && $floatingText->isInvisible() === true);
$splash = new \pocketmine\world\particle\PotionSplashParticle(new Color(9, 8, 7));
assert($splash->getColor()->toARGB() === (new Color(9, 8, 7))->toARGB());
assert(\pocketmine\world\particle\PotionSplashParticle::DEFAULT_COLOR() instanceof Color);

$apple = new \pocketmine\item\Apple();
assert($apple->getFoodRestore() === [] && $apple->getSaturationRestore() === []);
$armor = new \pocketmine\item\Armor();
assert($armor->getMaxStackSize() === 64 && $armor->getDefensePoints() === 0);
$armor->setCustomColor(new Color(10, 20, 30));
assert($armor->getCustomColor() instanceof Color);
$armor->clearCustomColor();
assert($armor->getCustomColor() === null);
$durable = new \pocketmine\item\Durable();
assert($durable->getDamage() === 0 && $durable->isBroken() === false);
assert($durable->setDamage(4) === $durable && $durable->getDamage() === 4);
assert($durable->setUnbreakable() === $durable && $durable->isUnbreakable() === true);
assert($durable->applyDamage(5) === false && $durable->getDamage() === 4);
$enchantmentItem = new \pocketmine\item\EnchantedBook();
$enchantmentItem->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2));
assert($enchantmentItem->hasEnchantments() === true);
assert(count($enchantmentItem->getEnchantments()) === 1);
$enchantmentItem->removeEnchantments();
assert($enchantmentItem->hasEnchantments() === false);
assert(\pocketmine\item\ItemIdentifier::fromBlock(VanillaBlocks::STONE())->getTypeId() === 'minecraft:stone');
assert(\pocketmine\item\ItemTypeIds::newId() !== \pocketmine\item\ItemTypeIds::newId());

$airBlock = new \pocketmine\block\Air();
assert($airBlock->canBePlaced() === false && $airBlock->canBeReplaced() === true);
assert($airBlock->getDrops(VanillaItems::STONE()) === []);
$anvil = new \pocketmine\block\Anvil();
assert($anvil->getDamage() === 0 && $anvil->place() === true);
assert($anvil->setDamage(2) === $anvil && $anvil->getDamage() === 2);
assert($anvil->getDropsForCompatibleTool(VanillaItems::STONE())[0] instanceof \pocketmine\item\Item);
$bamboo = new \pocketmine\block\Bamboo();
assert($bamboo->setReady(true) === $bamboo && $bamboo->isReady() === true);
assert($bamboo->setThick(true) === $bamboo && $bamboo->isThick() === true);
assert($bamboo->asItem() instanceof \pocketmine\item\Item);
$candle = new \pocketmine\block\Candle();
assert($candle->setCount(3) === $candle && $candle->getCount() === 3);
assert(\pocketmine\block\BlockTypeIds::newId() !== \pocketmine\block\BlockTypeIds::newId());

$redDye = \pocketmine\block\utils\DyeColor::RED();
assert($redDye === \pocketmine\block\utils\DyeColor::RED);
assert($redDye->getDisplayName() === 'Red');
assert($redDye->getRgbValue() instanceof Color);
assert(\pocketmine\block\utils\SupportType::FULL()->hasCenterSupport() === true);
assert(\pocketmine\block\utils\SupportType::EDGE()->hasEdgeSupport() === true);
assert(\pocketmine\block\utils\CopperOxidation::NONE()->getNext() === \pocketmine\block\utils\CopperOxidation::EXPOSED);
assert(\pocketmine\block\utils\DripleafState::FULL_TILT()->getScheduledUpdateDelayTicks() === 100);
assert(\pocketmine\block\utils\ChiseledBookshelfSlot::fromBlockFaceCoordinates(0.2, 0.8) === \pocketmine\block\utils\ChiseledBookshelfSlot::TOP_LEFT);
$bannerLayer = new \pocketmine\block\utils\BannerPatternLayer(\pocketmine\block\utils\BannerPatternType::CREEPER(), $redDye);
assert($bannerLayer->getType() === \pocketmine\block\utils\BannerPatternType::CREEPER && $bannerLayer->getColor() === $redDye);
assert(\pocketmine\block\utils\FortuneDropHelper::discrete(VanillaItems::STONE(), 1, 3) >= 1);
assert(\pocketmine\block\utils\FortuneDropHelper::bonusChanceFixed(VanillaItems::STONE(), 0.0, 0.0) === true);
$utilityBlock = new class extends \pocketmine\block\Block {
    use \pocketmine\block\utils\AgeableTrait;
    use \pocketmine\block\utils\LightableTrait;
    public function __construct() { parent::__construct('minecraft:utility', 'Utility'); }
};
assert($utilityBlock->setAge(4) === $utilityBlock && $utilityBlock->getAge() === 4);
assert($utilityBlock->setLit(true) === $utilityBlock && $utilityBlock->isLit() === true);

$tileChest = new \pocketmine\block\tile\Chest();
$tilePair = new \pocketmine\block\tile\Chest();
assert($tileChest->getInventory()->getSize() === 27 && $tileChest->getRealInventory() === $tileChest->getInventory());
assert($tileChest->isPaired() === false && $tileChest->pairWith($tilePair) === $tileChest);
assert($tileChest->isPaired() === true && $tileChest->getPair() === $tilePair);
assert($tileChest->unpair() === $tileChest && $tileChest->isPaired() === false);
$tileBarrel = new \pocketmine\block\tile\Barrel();
assert($tileBarrel->canOpenWith(VanillaItems::STONE()) === true);
assert($tileBarrel->close() === null && $tileBarrel->isClosed() === true);
$tileSign = new \pocketmine\block\tile\Sign();
$tileSignText = new SignText(['front']);
$tileBackText = new SignText(['back']);
assert($tileSign->setText($tileSignText) === $tileSign && $tileSign->getText() === $tileSignText);
assert($tileSign->setBackText($tileBackText) === $tileSign && $tileSign->getBackText() === $tileBackText);
assert($tileSign->setWaxed(true) === $tileSign && $tileSign->isWaxed() === true);
$tileFrame = new \pocketmine\block\tile\ItemFrame();
$tileFrameItem = VanillaItems::STONE();
assert($tileFrame->hasItem() === false);
assert($tileFrame->setItem($tileFrameItem) === $tileFrame && $tileFrame->hasItem() === true && $tileFrame->getItem() === $tileFrameItem);
assert($tileFrame->setItemRotation(2) === $tileFrame && $tileFrame->getItemRotation() === 2);
$tileCampfire = new \pocketmine\block\tile\Campfire();
assert($tileCampfire->getInventory()->getSize() === 4);
$tileSpawnable = new \pocketmine\block\tile\Spawnable();
assert($tileSpawnable->isDirty() === false && $tileSpawnable->setDirty(true) === $tileSpawnable && $tileSpawnable->isDirty() === true);
assert(\pocketmine\block\tile\TileFactory::isRegistered(\pocketmine\block\tile\Chest::class) === true);
assert(\pocketmine\block\tile\TileFactory::createFromData([]) instanceof \pocketmine\block\tile\Tile);
assert(\pocketmine\block\tile\TileFactory::getSaveId($tileChest) === \pocketmine\block\tile\Chest::class);

assert(\pocketmine\data\bedrock\BiomeIds::PLAINS === 1);
assert(\pocketmine\data\bedrock\EffectIds::SPEED === 1);
assert(\pocketmine\data\bedrock\EnchantmentIds::SHARPNESS === 9);
assert(\pocketmine\data\bedrock\WorldDataVersions::NETWORK === 827);
assert(\pocketmine\data\bedrock\WorldDataVersions::LAST_OPENED_IN === [1, 21, 100, 23, 0]);
assert(str_ends_with(\pocketmine\data\bedrock\BedrockDataFiles::ITEM_TAGS_JSON, '/item_tags.json'));
$dyeMap = new \pocketmine\data\bedrock\DyeColorIdMap();
assert($dyeMap->fromItemId(14) === \pocketmine\block\utils\DyeColor::RED);
assert($dyeMap->toItemId(\pocketmine\block\utils\DyeColor::RED) === 14);
assert($dyeMap->fromInvertedId(1) === \pocketmine\block\utils\DyeColor::RED);
$tagMap = new \pocketmine\data\bedrock\ItemTagToIdMap(['logs' => ['minecraft:oak_log']]);
assert($tagMap->tagContainsId('logs', 'minecraft:oak_log') === true);
$tagMap->addIdToTag('logs', 'minecraft:birch_log');
assert($tagMap->getIdsForTag('logs') === ['minecraft:oak_log', 'minecraft:birch_log']);
$legacyMap = new class([5 => 'minecraft:planks']) extends \pocketmine\data\bedrock\LegacyToStringIdMap {};
assert($legacyMap->legacyToString(5) === 'minecraft:planks');
$legacyMap->add('minecraft:stone', 1);
assert($legacyMap->getLegacyToStringMap()[1] === 'minecraft:stone');
$bannerIdMap = new \pocketmine\data\bedrock\BannerPatternTypeIdMap();
$bannerIdMap->register('cre', \pocketmine\block\utils\BannerPatternType::CREEPER);
assert($bannerIdMap->fromId('cre') === \pocketmine\block\utils\BannerPatternType::CREEPER);
assert($bannerIdMap->toId(\pocketmine\block\utils\BannerPatternType::CREEPER) === 'cre');
assert(\pocketmine\data\bedrock\NoteInstrumentIdMap::getInstance()->fromId(14) === \pocketmine\world\sound\NoteInstrument::BANJO);

$simpleInventory = new \pocketmine\inventory\SimpleInventory(2);
$slotChanges = [];
$contentChanges = 0;
$simpleInventory->addListener(new \pocketmine\inventory\CallbackInventoryListener(
    static function(\pocketmine\inventory\Inventory $inventory, int $slot, \pocketmine\item\Item $oldItem) use (&$slotChanges): void {
        $slotChanges[] = [$slot, $oldItem->getTypeId()];
    },
    static function(\pocketmine\inventory\Inventory $inventory, array $oldContents) use (&$contentChanges): void {
        $contentChanges++;
    }
));
$simpleInventory->setItem(0, VanillaItems::STONE());
assert($simpleInventory->getSize() === 2 && $simpleInventory->getItem(0)->getTypeId() === 'minecraft:stone');
assert($slotChanges === [[0, 'minecraft:air']]);
$simpleInventory->setContents([1 => VanillaItems::DIAMOND()]);
assert($contentChanges === 1 && $simpleInventory->getItem(1)->getTypeId() === 'minecraft:diamond');
assert($simpleInventory->getSlotValidators() instanceof \pocketmine\utils\ObjectSet);
$delegateInventory = new \pocketmine\inventory\DelegateInventory($simpleInventory);
$delegateInventory->setItem(0, VanillaItems::DIRT());
assert($delegateInventory->getItem(0)->getTypeId() === 'minecraft:dirt' && $simpleInventory->getItem(0)->getTypeId() === 'minecraft:dirt');
$playerInventory = new \pocketmine\inventory\PlayerInventory($sender);
assert($playerInventory->getHotbarSize() === 9 && $playerInventory->isHotbarSlot(8) === true && $playerInventory->isHotbarSlot(9) === false);
$playerInventory->setItemInHand(VanillaItems::DIAMOND());
assert($playerInventory->getItemInHand()->getTypeId() === 'minecraft:diamond');
$heldIndexChange = null;
$playerInventory->getHeldItemIndexChangeListeners()->add(static function(int $oldIndex) use (&$heldIndexChange): void { $heldIndexChange = $oldIndex; });
$playerInventory->setHeldItemIndex(1);
assert($playerInventory->getHeldItemIndex() === 1 && $heldIndexChange === 0);
$armorInventory = new \pocketmine\inventory\ArmorInventory($sender);
$armorInventory->setHelmet(VanillaItems::STONE());
$armorInventory->setBoots(VanillaItems::DIAMOND());
assert($armorInventory->getHelmet()->getTypeId() === 'minecraft:stone' && $armorInventory->getBoots()->getTypeId() === 'minecraft:diamond');
assert((new \pocketmine\inventory\PlayerCursorInventory($sender))->getSize() === 1);
assert((new \pocketmine\inventory\PlayerOffHandInventory($sender))->getSize() === 1);
assert((new \pocketmine\inventory\PlayerEnderInventory($sender))->getSize() === 27);
$creativeInventory = new \pocketmine\inventory\CreativeInventory();
$creativeInventory->add(VanillaItems::STONE(), \pocketmine\inventory\CreativeCategory::CONSTRUCTION);
assert($creativeInventory->contains(VanillaItems::STONE()) === true);
assert($creativeInventory->getEntry(0)?->getCategory() === \pocketmine\inventory\CreativeCategory::CONSTRUCTION);
$creativeInventory->remove(VanillaItems::STONE());
assert($creativeInventory->contains(VanillaItems::STONE()) === false);
$creativeGroup = new \pocketmine\inventory\CreativeGroup('Building', VanillaItems::DIRT());
assert($creativeGroup->getName() === 'Building' && $creativeGroup->getIcon()->getTypeId() === 'minecraft:dirt');
$creativeData = new \pocketmine\inventory\json\CreativeGroupData('group.name', ['name' => 'minecraft:stone'], [['name' => 'minecraft:dirt']]);
assert($creativeData->group_name === 'group.name' && count($creativeData->items) === 1);

$exactStone = new \pocketmine\crafting\ExactRecipeIngredient(VanillaItems::STONE());
$metaDiamond = new \pocketmine\crafting\MetaWildcardRecipeIngredient('minecraft:diamond');
\pocketmine\data\bedrock\ItemTagToIdMap::getInstance()->addIdToTag('compat:stones', 'minecraft:stone');
$tagStone = new \pocketmine\crafting\TagWildcardRecipeIngredient('compat:stones');
assert($exactStone->accepts(VanillaItems::STONE()) === true && $exactStone->accepts(VanillaItems::DIRT()) === false);
assert($metaDiamond->accepts(VanillaItems::DIAMOND()) === true && $tagStone->accepts(VanillaItems::STONE()) === true);
$craftingGrid = new \pocketmine\crafting\CraftingGrid(\pocketmine\crafting\CraftingGrid::SIZE_SMALL);
$craftingGrid->setItem(0, VanillaItems::STONE());
assert($craftingGrid->getRecipeWidth() === 1 && $craftingGrid->getRecipeHeight() === 1);
$shapedRecipe = new \pocketmine\crafting\ShapedRecipe(['S'], ['S' => $exactStone], [VanillaItems::DIAMOND()]);
assert($shapedRecipe->matchesCraftingGrid($craftingGrid) === true && $shapedRecipe->getResults()[0]->getTypeId() === 'minecraft:diamond');
$shapelessRecipe = new \pocketmine\crafting\ShapelessRecipe([$exactStone], [VanillaItems::DIRT()]);
assert($shapelessRecipe->matchesCraftingGrid($craftingGrid) === true && $shapelessRecipe->getIngredientCount() === 1);
$recipeManager = new \pocketmine\crafting\CraftingManager();
$recipeCallbacks = 0;
$recipeManager->getRecipeRegisteredCallbacks()->add(static function() use (&$recipeCallbacks): void { $recipeCallbacks++; });
$recipeManager->registerShapedRecipe($shapedRecipe);
$recipeManager->registerShapelessRecipe($shapelessRecipe);
assert($recipeCallbacks === 2);
assert($recipeManager->matchRecipe($craftingGrid, [VanillaItems::DIAMOND()]) === $shapedRecipe);
assert(iterator_to_array($recipeManager->matchRecipeByOutputs([VanillaItems::DIRT()]))[0] === $shapelessRecipe);
$furnaceRecipe = new \pocketmine\crafting\FurnaceRecipe(VanillaItems::DIAMOND(), $exactStone);
$recipeManager->getFurnaceRecipeManager(\pocketmine\crafting\FurnaceType::FURNACE)->register($furnaceRecipe);
assert($recipeManager->getFurnaceRecipeManager(\pocketmine\crafting\FurnaceType::FURNACE)->match(VanillaItems::STONE()) === $furnaceRecipe);
assert(\pocketmine\crafting\FurnaceType::BLAST_FURNACE->getCookDurationTicks() === 100);
$potionTypeRecipe = new \pocketmine\crafting\PotionTypeRecipe($exactStone, new \pocketmine\crafting\ExactRecipeIngredient(VanillaItems::DIRT()), VanillaItems::DIAMOND());
$recipeManager->registerPotionTypeRecipe($potionTypeRecipe);
assert($recipeManager->matchBrewingRecipe(VanillaItems::STONE(), VanillaItems::DIRT()) === $potionTypeRecipe);
$jsonStack = new \pocketmine\crafting\json\ItemStackData('minecraft:stone');
assert($jsonStack->jsonSerialize() === 'minecraft:stone');
assert(\pocketmine\crafting\json\RecipeIngredientData::WILDCARD_META_VALUE === 32767);

$plainBiome = new \pocketmine\world\biome\PlainBiome();
assert($plainBiome->getName() === 'Plains' && $plainBiome->getMinElevation() === 63 && $plainBiome->getMaxElevation() === 68);
assert($plainBiome->getTemperature() === 0.8 && $plainBiome->getRainfall() === 0.4);
assert($plainBiome->getGroundCover()[0]->getTypeId() === 'minecraft:grass');
$biomeRegistry = new \pocketmine\world\biome\BiomeRegistry();
assert($biomeRegistry->getBiome(\pocketmine\data\bedrock\BiomeIds::PLAINS)->getName() === 'Plains');
assert($biomeRegistry->getBiome(250)->getName() === 'Unknown' && $biomeRegistry->getBiome(250)->getId() === 250);
$customBiome = new \pocketmine\world\biome\DesertBiome();
$biomeRegistry->register(201, $customBiome);
assert($biomeRegistry->getBiome(201) === $customBiome && $customBiome->getId() === 201);
$biomeColor = new \pocketmine\world\biome\model\ColorData(1, 2, 3, 4);
$biomeDefinition = new \pocketmine\world\biome\model\BiomeDefinitionEntryData(1, 0.7, 0.4, 0.0, 0.1, 0.2, $biomeColor, true, ['overworld']);
assert($biomeDefinition->mapWaterColour === $biomeColor && $biomeDefinition->tags === ['overworld']);

$stateWriter = \pocketmine\data\bedrock\block\convert\BlockStateWriter::create('minecraft:test');
$stateWriter->writeBool('lit', true)->writeInt('age', 4)->writeString('mode', 'open');
$stateData = $stateWriter->getBlockStateData();
assert($stateData->getName() === 'minecraft:test' && $stateData->getState('age')->getValue() === 4);
assert($stateData->equals(\pocketmine\data\bedrock\block\BlockStateData::fromNbt($stateData->toNbt())) === true);
$stateReader = new \pocketmine\data\bedrock\block\convert\BlockStateReader($stateData);
assert($stateReader->readBool('lit') === true && $stateReader->readInt('age') === 4 && $stateReader->readString('mode') === 'open');
$stateMap = \pocketmine\data\bedrock\block\convert\property\IntFromRawStateMap::string([1 => 'one', 2 => 'two']);
assert($stateMap->rawToValue('two') === 2 && $stateMap->valueToRaw(1) === 'one');
$enumMap = \pocketmine\data\bedrock\block\convert\property\EnumFromRawStateMap::string(\pocketmine\crafting\ShapelessRecipeType::class, static fn(\pocketmine\crafting\ShapelessRecipeType $type): string => strtolower($type->name));
assert($enumMap->rawToValue('crafting') === \pocketmine\crafting\ShapelessRecipeType::CRAFTING);
$propertyTarget = new class {
    public bool $lit = false;
    public int $age = 0;
    public int $mapped = 1;
    public array $flags = [];
};
$boolProperty = new \pocketmine\data\bedrock\block\convert\property\BoolProperty('lit', static fn(object $block): bool => $block->lit, static function(object $block, bool $value): void { $block->lit = $value; });
$intProperty = new \pocketmine\data\bedrock\block\convert\property\IntProperty('age', 0, 7, static fn(object $block): int => $block->age, static function(object $block, int $value): void { $block->age = $value; });
$reader = new \pocketmine\data\bedrock\block\convert\BlockStateReader(\pocketmine\data\bedrock\block\BlockStateData::current('minecraft:test', ['lit' => true, 'age' => 6]));
$boolProperty->deserialize($propertyTarget, $reader);
$intProperty->deserialize($propertyTarget, $reader);
assert($propertyTarget->lit === true && $propertyTarget->age === 6);
$propertyWriter = \pocketmine\data\bedrock\block\convert\BlockStateWriter::create('minecraft:test');
$boolProperty->serialize($propertyTarget, $propertyWriter);
$intProperty->serialize($propertyTarget, $propertyWriter);
assert($propertyWriter->getBlockStateData()->getState('lit')->getValue() === 1 && $propertyWriter->getBlockStateData()->getState('age')->getValue() === 6);
$stringBool = new \pocketmine\data\bedrock\block\convert\property\BoolFromStringProperty('lit_string', 'false', 'true', static fn(object $block): bool => $block->lit, static function(object $block, bool $value): void { $block->lit = $value; });
$stringBool->deserializePlain($propertyTarget, 'false');
assert($propertyTarget->lit === false && $stringBool->serializePlain($propertyTarget) === 'false');
$valueProperty = new \pocketmine\data\bedrock\block\convert\property\ValueFromStringProperty('mapped', $stateMap, static fn(object $block): int => $block->mapped, static function(object $block, int $value): void { $block->mapped = $value; });
$valueProperty->deserializePlain($propertyTarget, 'two');
assert($propertyTarget->mapped === 2 && $valueProperty->serializePlain($propertyTarget) === 'two');
$flagMap = \pocketmine\data\bedrock\block\convert\property\IntFromRawStateMap::int([1 => 1, 2 => 2, 3 => 4]);
$flagProperty = new \pocketmine\data\bedrock\block\convert\property\ValueSetFromIntProperty('flags', $flagMap, static fn(object $block): array => $block->flags, static function(object $block, array $value): void { $block->flags = $value; });
$flagProperty->deserialize($propertyTarget, new \pocketmine\data\bedrock\block\convert\BlockStateReader(\pocketmine\data\bedrock\block\BlockStateData::current('minecraft:test', ['flags' => 3])));
assert($propertyTarget->flags === [1, 2]);
assert(\pocketmine\data\bedrock\block\convert\property\WallConnectionTypeShim::serialize(\pocketmine\block\utils\WallConnectionType::TALL)->getValue() === 'tall');
assert(\pocketmine\data\bedrock\block\convert\property\FlattenedCaveVinesVariant::HEAD_WITH_BERRIES->value === '_head_with_berries');

$chunkData = new \pocketmine\world\format\io\ChunkData([0 => 'subchunk'], true, ['entity'], ['tile']);
$loadedChunkData = new \pocketmine\world\format\io\LoadedChunkData($chunkData, true, \pocketmine\world\format\io\LoadedChunkData::FIXER_FLAG_ALL);
assert($chunkData->isPopulated() === true && $chunkData->getSubChunks()[0] === 'subchunk');
assert($loadedChunkData->getData() === $chunkData && $loadedChunkData->isUpgraded() === true);
$provider = new class('/tmp/pmmpcompat-world') extends \pocketmine\world\format\io\BaseWorldProvider implements \pocketmine\world\format\io\WritableWorldProvider {
    public function saveChunk(int $chunkX, int $chunkZ, \pocketmine\world\format\io\ChunkData $chunkData, int $dirtyFlags): void {}
};
assert($provider->getPath() === '/tmp/pmmpcompat-world' && $provider->getWorldData()->getName() === 'pmmpcompat-world');
assert($provider->getWorldMinY() === -64 && $provider->getWorldMaxY() === 320 && $provider->calculateChunkCount() === 0);
$managerEntry = new \pocketmine\world\format\io\WritableWorldProviderManagerEntry(
    static fn(string $path): bool => $path === '/tmp/pmmpcompat-world',
    static fn(string $path, \Logger $logger): \pocketmine\world\format\io\WritableWorldProvider => new class($path) extends \pocketmine\world\format\io\BaseWorldProvider implements \pocketmine\world\format\io\WritableWorldProvider {
        public function saveChunk(int $chunkX, int $chunkZ, \pocketmine\world\format\io\ChunkData $chunkData, int $dirtyFlags): void {}
    },
    static function(string $path, string $name, mixed $options): void {}
);
$providerManager = new \pocketmine\world\format\io\WorldProviderManager();
$providerManager->addProvider($managerEntry, 'compat', true);
assert($providerManager->getProviderByName('compat') === $managerEntry && $providerManager->getMatchingProviders('/tmp/pmmpcompat-world')['compat'] === $managerEntry);
$biomeBytes = \pocketmine\world\format\io\ChunkUtils::convertBiomeColors([0 => 0x01000000, 1 => 0x02000000]);
assert(strlen($biomeBytes) === 256 && ord($biomeBytes[0]) === 1 && count(\pocketmine\world\format\io\ChunkUtils::extrapolate3DBiomes($biomeBytes)) === 16);
assert(\pocketmine\world\format\io\FastChunkSerializer::deserializeTerrain(\pocketmine\world\format\io\FastChunkSerializer::serializeTerrain($chunkData)) instanceof \pocketmine\world\format\io\ChunkData);
assert(\pocketmine\world\format\io\GlobalBlockStateHandlers::getUnknownBlockStateData()->getName() === 'minecraft:info_update');
assert(\pocketmine\world\format\io\GlobalItemDataHandlers::getSerializer()->serializeType(VanillaItems::STONE())->getName() === 'minecraft:stone');
$formatConverter = new \pocketmine\world\format\io\FormatConverter($provider, $managerEntry, sys_get_temp_dir());
assert(str_contains($formatConverter->getBackupPath(), 'pmmpcompat-world'));

$location = new \pocketmine\world\format\io\region\RegionLocationTableEntry(2, 3, 123);
assert($location->getFirstSector() === 2 && $location->getLastSector() === 4);
assert($location->getUsedSectors() === [2, 3, 4]);
assert($location->overlaps(new \pocketmine\world\format\io\region\RegionLocationTableEntry(4, 1, 0)) === true);
$garbage = new \pocketmine\world\format\io\region\RegionGarbageMap([
    new \pocketmine\world\format\io\region\RegionLocationTableEntry(2, 2, 0),
    new \pocketmine\world\format\io\region\RegionLocationTableEntry(4, 1, 0),
]);
assert($garbage->end()?->getSectorCount() === 3);
$allocated = $garbage->allocate(2);
assert($allocated?->getFirstSector() === 2 && $garbage->end()?->getFirstSector() === 4);
$regionFile = $dir . '/region-loader/r.0.0.mca';
$regionLoader = \pocketmine\world\format\io\region\RegionLoader::createNew($regionFile);
$regionLoader->writeChunk(1, 2, 'chunk-payload');
assert($regionLoader->chunkExists(1, 2) === true && $regionLoader->readChunk(1, 2) === 'chunk-payload');
$regionLoader->close();
$regionLoaderReloaded = \pocketmine\world\format\io\region\RegionLoader::loadExisting($regionFile);
assert($regionLoaderReloaded->calculateChunkCount() === 1 && $regionLoaderReloaded->readChunk(1, 2) === 'chunk-payload');
$regionLoaderReloaded->removeChunk(1, 2);
assert($regionLoaderReloaded->chunkExists(1, 2) === false);
\pocketmine\world\format\io\region\Anvil::generate($dir . '/region-world', 'RegionWorld', null);
assert(\pocketmine\world\format\io\region\Anvil::isValid($dir . '/region-world') === false);
$anvilProvider = new \pocketmine\world\format\io\region\Anvil($dir . '/region-world');
$anvilProvider->saveChunk(33, 34, $chunkData, 0);
assert(\pocketmine\world\format\io\region\Anvil::isValid($dir . '/region-world') === true);
$loadedRegionChunk = $anvilProvider->loadChunk(33, 34);
assert($loadedRegionChunk?->getData()->getSubChunks()[0] === 'subchunk');
assert($anvilProvider->calculateChunkCount() === 1);
$allRegionChunks = iterator_to_array($anvilProvider->getAllChunks(), false);
assert(count($allRegionChunks) === 1);
assert($anvilProvider->getWorldMinY() === 0 && $anvilProvider->getWorldMaxY() === 256);
\pocketmine\world\format\io\region\RegionWorldProvider::getRegionIndex(33, 34, $regionX, $regionZ);
assert($regionX === 1 && $regionZ === 1);
assert((new \pocketmine\world\format\io\region\McRegion($dir . '/region-world'))->getWorldMaxY() === 128);
assert((new \pocketmine\world\format\io\region\PMAnvil($dir . '/region-world'))->getWorldMaxY() === 256);

assert(\pocketmine\world\format\io\leveldb\ChunkDataKey::SUBCHUNK === "\x2f");
assert(\pocketmine\world\format\io\leveldb\SubChunkVersion::PALETTED_MULTI_WITH_OFFSET === 9);
assert(\pocketmine\world\format\io\leveldb\ChunkVersion::v1_21_40 === 41);
\pocketmine\world\format\io\leveldb\LevelDB::generate($dir . '/leveldb-world', 'LevelDBWorld', null);
assert(\pocketmine\world\format\io\leveldb\LevelDB::isValid($dir . '/leveldb-world') === true);
$levelDbProvider = new \pocketmine\world\format\io\leveldb\LevelDB($dir . '/leveldb-world');
$levelDbProvider->saveChunk(-1, 2, $chunkData, 0);
assert(strlen(\pocketmine\world\format\io\leveldb\LevelDB::chunkIndex(-1, 2)) === 8);
assert($levelDbProvider->calculateChunkCount() === 1);
assert($levelDbProvider->loadChunk(-1, 2)?->getData()->getTileNBT() === ['tile']);
$levelDbRaw = $levelDbProvider->getDatabase()->get(\pocketmine\world\format\io\leveldb\LevelDB::chunkIndex(-1, 2) . \pocketmine\world\format\io\leveldb\ChunkDataKey::NEW_VERSION);
assert($levelDbRaw === chr(\pocketmine\world\format\io\leveldb\ChunkVersion::v1_21_40));
assert(count(iterator_to_array($levelDbProvider->getAllChunks(), false)) === 1);
$levelDbProvider->close();
$levelDbReloaded = new \pocketmine\world\format\io\leveldb\LevelDB($dir . '/leveldb-world');
assert($levelDbReloaded->loadChunk(-1, 2)?->getData()->getEntityNBT() === ['entity']);

$inventoryHolder = new Position(0, 64, 0, null);
$furnaceInventory = new \pocketmine\block\inventory\FurnaceInventory($inventoryHolder);
assert($furnaceInventory->getHolder() === $inventoryHolder && $furnaceInventory->getSize() === 3);
$furnaceInventory->setSmelting(VanillaItems::STONE());
$furnaceInventory->setFuel(VanillaItems::DIRT());
$furnaceInventory->setResult(VanillaItems::DIAMOND());
assert($furnaceInventory->getSmelting()->getTypeId() === 'minecraft:stone');
assert($furnaceInventory->getFuel()->getTypeId() === 'minecraft:dirt');
assert($furnaceInventory->getResult()->getTypeId() === 'minecraft:diamond');
$campfireInventory = new \pocketmine\block\inventory\CampfireInventory($inventoryHolder);
assert($campfireInventory->getSize() === 4 && $campfireInventory->getMaxStackSize() === 1);
$chestInventory = new \pocketmine\block\inventory\ChestInventory($inventoryHolder);
assert($chestInventory->getSize() === 27 && $chestInventory->getViewerCount() === 0);
$doubleChestInventory = new \pocketmine\block\inventory\DoubleChestInventory($chestInventory, new \pocketmine\block\inventory\ChestInventory($inventoryHolder));
assert($doubleChestInventory->getSize() === 54 && $doubleChestInventory->getLeftSide() === $chestInventory);
$enchantInventory = new \pocketmine\block\inventory\EnchantInventory($inventoryHolder);
assert(\pocketmine\block\inventory\EnchantInventory::SLOT_LAPIS === 1);
assert($enchantInventory->getInput()->isNull() === true && $enchantInventory->getLapis()->isNull() === true);

$deps = sys_get_temp_dir() . '/pmmpcompat-smoke-deps-' . getmypid();
@mkdir($deps . '/alpha/src/DepFixture', 0777, true);
@mkdir($deps . '/beta/src/DepFixture', 0777, true);
file_put_contents($deps . '/alpha/plugin.yml', "name: AlphaPlugin\nmain: DepFixture\\AlphaPlugin\nversion: 1.0.0\nloadbefore: [BetaPlugin]\n");
file_put_contents($deps . '/alpha/src/DepFixture/AlphaPlugin.php', "<?php\nnamespace DepFixture;\nclass AlphaPlugin extends \\pocketmine\\plugin\\PluginBase {}\n");
file_put_contents($deps . '/beta/plugin.yml', "name: BetaPlugin\nmain: DepFixture\\BetaPlugin\nversion: 1.0.0\nsoftdepend: [AlphaPlugin]\n");
file_put_contents($deps . '/beta/src/DepFixture/BetaPlugin.php', "<?php\nnamespace DepFixture;\nclass BetaPlugin extends \\pocketmine\\plugin\\PluginBase {}\n");
$ordered = (new PluginLoader(new Server()))->loadDirectory($deps);
assert(array_map(static fn($plugin) => $plugin->getName(), $ordered) === ['AlphaPlugin', 'BetaPlugin']);
$loadErrors = 0;
$loadedViaManager = (new Server())->getPluginManager()->loadPlugins($deps, $loadErrors);
assert($loadErrors === 0);
assert(array_map(static fn($plugin) => $plugin->getName(), $loadedViaManager) === ['AlphaPlugin', 'BetaPlugin']);

echo "pmmpcompat smoke ok\n";
