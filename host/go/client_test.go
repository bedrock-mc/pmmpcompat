package pmmpcompat

import (
	"context"
	"os"
	"path/filepath"
	"reflect"
	"runtime"
	"strings"
	"testing"
	"time"
)

func TestClientDrivesPMMPRuntimeProcess(t *testing.T) {
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	root := packageRoot(t)
	pluginsDir := t.TempDir()
	writeEchoPlugin(t, pluginsDir)
	phpBinary := os.Getenv("PMMPCOMPAT_PHP")
	if phpBinary == "" {
		phpBinary = "php"
	}
	phpArgs := strings.Fields(os.Getenv("PMMPCOMPAT_PHP_ARGS"))

	client, err := StartWithArgs(ctx, phpBinary, phpArgs, filepath.Join(root, "bin", "pmmpcompat-runtime.php"), pluginsDir)
	if err != nil {
		t.Fatalf("start runtime: %v", err)
	}
	defer func() {
		if err := client.Close(); err != nil {
			t.Fatalf("close runtime: %v", err)
		}
	}()

	load, actions, err := client.Load(ctx)
	if err != nil {
		t.Fatalf("load: %v", err)
	}
	if !reflect.DeepEqual(load.Plugins, []string{"EchoPlugin"}) {
		t.Fatalf("plugins = %#v", load.Plugins)
	}
	if len(actions) != 0 {
		t.Fatalf("load actions = %#v", actions)
	}

	if actions, err = client.Enable(ctx); err != nil {
		t.Fatalf("enable: %v", err)
	}
	if len(actions) != 0 {
		t.Fatalf("enable actions = %#v", actions)
	}

	commands, actions, err := client.Commands(ctx)
	if err != nil {
		t.Fatalf("commands: %v", err)
	}
	if len(actions) != 0 {
		t.Fatalf("commands actions = %#v", actions)
	}
	if len(commands.Commands) != 5 || commands.Commands[0].Name != "echo" {
		t.Fatalf("commands result = %#v", commands)
	}
	if commands.Commands[0].Usage != "/echo <message...>" {
		t.Fatalf("echo usage = %q", commands.Commands[0].Usage)
	}
	if commands.Commands[4].Name != "dynusage" || commands.Commands[4].Usage != "/dynusage <value:string>" {
		t.Fatalf("dynamic usage command = %#v", commands.Commands[4])
	}

	join, actions, err := client.PlayerJoin(ctx, "00000000-0000-4000-8000-000000000401", "Steve")
	if err != nil {
		t.Fatalf("join: %v", err)
	}
	if join.Player.Name != "Steve" || join.JoinMessage != "Steve joined the game" {
		t.Fatalf("join result = %#v", join)
	}
	wantActions := []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "welcome Steve"}}
	if len(actions) != 3 {
		t.Fatalf("join actions = %#v", actions)
	}
	if !reflect.DeepEqual(actions[:1], wantActions) {
		t.Fatalf("join message action = %#v, want %#v", actions[:1], wantActions)
	}
	if actions[1].Type != "player.send_form" || actions[1].UUID != "00000000-0000-4000-8000-000000000401" || actions[1].FormID != 1 {
		t.Fatalf("join form action = %#v", actions[1])
	}
	if actions[2].Type != "player.inventory.set_item" || actions[2].UUID != "00000000-0000-4000-8000-000000000401" || actions[2].Slot != 0 {
		t.Fatalf("join inventory action = %#v", actions[2])
	}
	if len(actions[2].Item) == 0 {
		t.Fatalf("join inventory item payload is empty: %#v", actions[2])
	}

	if _, actions, err = client.PlayerJoin(ctx, "00000000-0000-4000-8000-000000000402", "Alex"); err != nil {
		t.Fatalf("damager join: %v", err)
	}
	if len(actions) != 3 {
		t.Fatalf("damager join actions = %#v", actions)
	}

	form, actions, err := client.FormResponse(ctx, "00000000-0000-4000-8000-000000000401", 1, 0)
	if err != nil {
		t.Fatalf("form response: %v", err)
	}
	if !form.Handled {
		t.Fatalf("form response result = %#v", form)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "form response 0"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("form response actions = %#v, want %#v", actions, wantActions)
	}

	inventory, actions, err := client.PlayerInventory(ctx, "00000000-0000-4000-8000-000000000401", []InventorySlot{{
		Slot: 5,
		Item: InventoryItem{TypeID: "minecraft:diamond", Name: "Diamond", Count: 7},
	}})
	if err != nil {
		t.Fatalf("inventory sync: %v", err)
	}
	if !inventory.Synced {
		t.Fatalf("inventory sync result = %#v", inventory)
	}
	if len(actions) != 0 {
		t.Fatalf("inventory sync actions = %#v", actions)
	}

	command, actions, err := client.Command(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "slot", []string{"5"})
	if err != nil {
		t.Fatalf("slot command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("slot command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "slot 5 minecraft:diamond 7"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("slot command actions = %#v, want %#v", actions, wantActions)
	}

	state, actions, err := client.PlayerState(ctx, "00000000-0000-4000-8000-000000000401", PlayerState{
		Position:   &Position{X: 10.5, Y: 64, Z: -3.25, World: "arena"},
		Health:     floatPtr(14.5),
		MaxHealth:  floatPtr(30),
		Gamemode:   "adventure",
		XPLevel:    intPtr(9),
		XPProgress: floatPtr(0.75),
	})
	if err != nil {
		t.Fatalf("state sync: %v", err)
	}
	if !state.Synced {
		t.Fatalf("state sync result = %#v", state)
	}
	if len(actions) != 0 {
		t.Fatalf("state sync actions = %#v", actions)
	}

	command, actions, err = client.Command(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "state", nil)
	if err != nil {
		t.Fatalf("state command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("state command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "state arena 10.5 64 -3.25 14.5/30 Adventure 9 0.75"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("state command actions = %#v, want %#v", actions, wantActions)
	}

	command, actions, err = client.Command(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "mutate", nil)
	if err != nil {
		t.Fatalf("mutate command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("mutate command result = %#v", command)
	}
	wantActions = []Action{
		{Type: "player.set_health", UUID: "00000000-0000-4000-8000-000000000401", Health: 6, MaxHealth: 30},
		{Type: "player.set_gamemode", UUID: "00000000-0000-4000-8000-000000000401", Gamemode: "creative"},
		{Type: "player.set_experience", UUID: "00000000-0000-4000-8000-000000000401", XPLevel: 12, XPProgress: 0.75},
		{Type: "player.set_experience", UUID: "00000000-0000-4000-8000-000000000401", XPLevel: 12, XPProgress: 0.5},
		{Type: "player.teleport", UUID: "00000000-0000-4000-8000-000000000401", Position: &Position{X: 1, Y: 2, Z: 3, World: "mutated"}},
		{Type: "player.send_popup", UUID: "00000000-0000-4000-8000-000000000401", Message: "popup"},
		{Type: "player.send_tip", UUID: "00000000-0000-4000-8000-000000000401", Message: "tip"},
		{Type: "player.send_actionbar", UUID: "00000000-0000-4000-8000-000000000401", Message: "actionbar"},
		{Type: "player.send_title", UUID: "00000000-0000-4000-8000-000000000401", Title: "title", Subtitle: "subtitle"},
		{Type: "player.set_title_duration", UUID: "00000000-0000-4000-8000-000000000401", FadeIn: 5, Stay: 40, FadeOut: 5},
		{Type: "player.reset_titles", UUID: "00000000-0000-4000-8000-000000000401"},
		{Type: "player.remove_titles", UUID: "00000000-0000-4000-8000-000000000401"},
		{Type: "player.set_allow_flight", UUID: "00000000-0000-4000-8000-000000000401", Value: boolPtr(true)},
		{Type: "player.set_flying", UUID: "00000000-0000-4000-8000-000000000401", Value: boolPtr(true)},
		{Type: "player.set_flight_speed", UUID: "00000000-0000-4000-8000-000000000401", Speed: 0.2},
		{Type: "player.set_view_distance", UUID: "00000000-0000-4000-8000-000000000401", Distance: 8},
		{Type: "player.transfer", UUID: "00000000-0000-4000-8000-000000000401", Address: "127.0.0.1", Port: 19133, Message: "switching"},
	}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("mutate command actions = %#v, want %#v", actions, wantActions)
	}

	chat, actions, err := client.Chat(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "hello")
	if err != nil {
		t.Fatalf("chat: %v", err)
	}
	if chat.Message != "HELLO" || chat.FormattedMessage != "<Steve> HELLO" || chat.Cancelled {
		t.Fatalf("chat result = %#v", chat)
	}
	if len(actions) != 0 {
		t.Fatalf("chat actions = %#v", actions)
	}

	command, actions, err = client.Command(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "echo", []string{"from", "go"})
	if err != nil {
		t.Fatalf("command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "echo from go"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("command actions = %#v, want %#v", actions, wantActions)
	}

	command, actions, err = client.Command(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "rewrite", nil)
	if err != nil {
		t.Fatalf("rewrite command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("rewrite command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "echo command-event"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("rewrite command actions = %#v, want %#v", actions, wantActions)
	}

	command, actions, err = client.Command(ctx, "00000000-0000-4000-8000-000000000401", "Steve", "deny", nil)
	if err != nil {
		t.Fatalf("deny command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("deny command result = %#v", command)
	}
	if len(actions) != 0 {
		t.Fatalf("deny command actions = %#v", actions)
	}

	breakResult, actions, err := client.BlockBreak(ctx, "00000000-0000-4000-8000-000000000401", "Steve", Position{X: 11, Y: 65, Z: -2}, &Block{TypeID: "minecraft:stone", Name: "Stone"}, nil)
	if err != nil {
		t.Fatalf("block break: %v", err)
	}
	if breakResult.Cancelled || breakResult.Block == nil || breakResult.Block.TypeID != "minecraft:stone" || breakResult.Position.X != 11 {
		t.Fatalf("block break result = %#v", breakResult)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "break minecraft:stone 11 65 -2"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("block break actions = %#v, want %#v", actions, wantActions)
	}

	placeResult, actions, err := client.BlockPlace(ctx, "00000000-0000-4000-8000-000000000401", "Steve", Position{X: 12, Y: 66, Z: -3}, &Block{TypeID: "minecraft:dirt", Name: "Dirt"}, nil)
	if err != nil {
		t.Fatalf("block place: %v", err)
	}
	if placeResult.Cancelled || len(placeResult.Blocks) != 1 || placeResult.Blocks[0].Block.TypeID != "minecraft:dirt" {
		t.Fatalf("block place result = %#v", placeResult)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "place minecraft:dirt 12 66 -3"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("block place actions = %#v, want %#v", actions, wantActions)
	}

	interactResult, actions, err := client.PlayerInteract(ctx, "00000000-0000-4000-8000-000000000401", "Steve", Position{X: 13, Y: 67, Z: -4}, 1, &Block{TypeID: "minecraft:stone", Name: "Stone"}, nil)
	if err != nil {
		t.Fatalf("player interact: %v", err)
	}
	if interactResult.Cancelled || !interactResult.UseItem || !interactResult.UseBlock || interactResult.Position.X != 13 {
		t.Fatalf("player interact result = %#v", interactResult)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "interact minecraft:stone 13 67 -4"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("player interact actions = %#v, want %#v", actions, wantActions)
	}

	damageResult, actions, err := client.EntityDamage(ctx, "00000000-0000-4000-8000-000000000401", "Steve", 5, 1, "00000000-0000-4000-8000-000000000402", "Alex")
	if err != nil {
		t.Fatalf("entity damage: %v", err)
	}
	if damageResult.Cancelled || damageResult.Damager == nil || damageResult.Damager.Name != "Alex" || damageResult.FinalDamage != 4 {
		t.Fatalf("entity damage result = %#v", damageResult)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000402", Message: "damage Steve 4"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("entity damage actions = %#v, want %#v", actions, wantActions)
	}

	deathResult, actions, err := client.PlayerDeath(ctx, "00000000-0000-4000-8000-000000000401", "Steve", 7, "Steve died")
	if err != nil {
		t.Fatalf("player death: %v", err)
	}
	if deathResult.DeathMessage != "Steve died" || !deathResult.KeepInventory || deathResult.XP != 7 {
		t.Fatalf("player death result = %#v", deathResult)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "00000000-0000-4000-8000-000000000401", Message: "death by Alex"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("player death actions = %#v, want %#v", actions, wantActions)
	}

	respawnResult, actions, err := client.PlayerRespawn(ctx, "00000000-0000-4000-8000-000000000401", "Steve", nil)
	if err != nil {
		t.Fatalf("player respawn: %v", err)
	}
	if respawnResult.Position.World != "respawned" || respawnResult.Position.X != 20 {
		t.Fatalf("player respawn result = %#v", respawnResult)
	}
	if len(actions) != 0 {
		t.Fatalf("player respawn actions = %#v", actions)
	}

	if actions, err = client.Disable(ctx); err != nil {
		t.Fatalf("disable: %v", err)
	}
	if len(actions) != 0 {
		t.Fatalf("disable actions = %#v", actions)
	}
}

func packageRoot(t *testing.T) string {
	t.Helper()
	_, file, _, ok := runtime.Caller(0)
	if !ok {
		t.Fatal("runtime caller failed")
	}
	return filepath.Clean(filepath.Join(filepath.Dir(file), "..", ".."))
}

func writeEchoPlugin(t *testing.T, pluginsDir string) {
	t.Helper()
	pluginDir := filepath.Join(pluginsDir, "EchoPlugin", "src", "IpcFixture")
	if err := os.MkdirAll(pluginDir, 0o755); err != nil {
		t.Fatalf("mkdir plugin: %v", err)
	}
	writeFile(t, filepath.Join(pluginsDir, "EchoPlugin", "plugin.yml"), `name: EchoPlugin
main: IpcFixture\EchoPlugin
version: 1.0.0
commands:
  echo:
    description: Echo command
    usage: /echo <message...>
  slot:
    description: Report inventory slot
  state:
    description: Report player state
  mutate:
    description: Mutate player state
`)
	writeFile(t, filepath.Join(pluginDir, "EchoPlugin.php"), `<?php
namespace IpcFixture;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\form\SimpleForm;
use pocketmine\player\GameMode;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\Position;
use pocketmine\world\World;

class EchoPlugin extends PluginBase implements Listener {
    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register('fixture', new class('dynusage') extends Command {
            public function __construct(string $name) {
                parent::__construct($name);
                $this->usageMessage = '/dynusage <value:string>';
            }

            public function execute(CommandSender $sender, string $label, array $args): bool {
                $sender->sendMessage('dynusage ' . implode(' ', $args));
                return true;
            }
        });
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $event->getPlayer()->sendMessage('welcome ' . $event->getPlayer()->getName());
        $event->getPlayer()->sendForm((new SimpleForm('Greeting', 'Pick one', static function(Player $player, mixed $data): void {
            $player->sendMessage('form response ' . (string) $data);
        }))->addButton('Accept'));
        $event->getPlayer()->getInventory()->setItem(0, VanillaItems::DIAMOND()->setCount(3));
    }

    public function onChat(PlayerChatEvent $event): void {
        $event->setMessage(strtoupper($event->getMessage()));
    }

    public function onCommandEvent(CommandEvent $event): void {
        if($event->getCommand() === '/rewrite') {
            $event->setCommand('/echo command-event');
        }
        if($event->getCommand() === '/deny') {
            $event->cancel();
        }
    }

    public function onBreak(BlockBreakEvent $event): void {
        $pos = $event->getBlock()->getPosition();
        $event->getPlayer()->sendMessage('break ' . $event->getBlock()->getTypeId() . ' ' . $pos->getFloorX() . ' ' . $pos->getFloorY() . ' ' . $pos->getFloorZ());
    }

    public function onPlace(BlockPlaceEvent $event): void {
        foreach($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
            $event->getPlayer()->sendMessage('place ' . $block->getTypeId() . ' ' . $x . ' ' . $y . ' ' . $z);
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $pos = $event->getBlock()->getPosition();
        $event->getPlayer()->sendMessage('interact ' . $event->getBlock()->getTypeId() . ' ' . $pos->getFloorX() . ' ' . $pos->getFloorY() . ' ' . $pos->getFloorZ());
    }

    public function onDamage(EntityDamageByEntityEvent $event): void {
        if($event->getEntity() instanceof Player && $event->getDamager() instanceof Player) {
            $event->setModifier(-1, 12345);
            $event->getDamager()->sendMessage('damage ' . $event->getEntity()->getName() . ' ' . $event->getFinalDamage());
        }
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $cause = $event->getPlayer()->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent && $cause->getDamager() instanceof Player) {
            $event->getPlayer()->sendMessage('death by ' . $cause->getDamager()->getName());
        }
        $event->setKeepInventory(true);
    }

    public function onRespawn(PlayerRespawnEvent $event): void {
        $event->setRespawnPosition(new Position(20, 70, 20, new World('respawned')));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() === 'slot' && $sender instanceof Player) {
            $slot = (int) ($args[0] ?? 0);
            $item = $sender->getInventory()->getItem($slot);
            $sender->sendMessage('slot ' . $slot . ' ' . $item->getTypeId() . ' ' . $item->getCount());
            return true;
        }
        if($command->getName() === 'state' && $sender instanceof Player) {
            $pos = $sender->getPosition();
            $sender->sendMessage('state ' . $pos->getWorld()->getFolderName() . ' ' . $pos->x . ' ' . $pos->y . ' ' . $pos->z . ' ' . $sender->getHealth() . '/' . $sender->getMaxHealth() . ' ' . $sender->getGamemode()->getEnglishName() . ' ' . $sender->getXpLevel() . ' ' . $sender->getXpProgress());
            return true;
        }
        if($command->getName() === 'mutate' && $sender instanceof Player) {
            $sender->setHealth(6);
            $sender->setGamemode(GameMode::CREATIVE());
            $sender->setXpLevel(12);
            $sender->setXpProgress(0.5);
            $sender->teleport(new Position(1, 2, 3, new World('mutated')));
            $sender->sendPopup('popup');
            $sender->sendTip('tip');
            $sender->sendActionBarMessage('actionbar');
            $sender->sendTitle('title', 'subtitle');
            $sender->setTitleDuration(5, 40, 5);
            $sender->resetTitles();
            $sender->removeTitles();
            $sender->setAllowFlight(true);
            $sender->setFlying(true);
            $sender->setFlightSpeedMultiplier(0.2);
            $sender->setViewDistance(8);
            $sender->transfer('127.0.0.1', 19133, 'switching');
            return true;
        }
        $sender->sendMessage('echo ' . implode(' ', $args));
        return true;
    }
}
`)
}

func floatPtr(v float64) *float64 {
	return &v
}

func intPtr(v int) *int {
	return &v
}

func boolPtr(v bool) *bool {
	return &v
}

func writeFile(t *testing.T, path, content string) {
	t.Helper()
	if err := os.WriteFile(path, []byte(content), 0o644); err != nil {
		t.Fatalf("write %s: %v", path, err)
	}
}
