package pmmpcompat

import (
	"context"
	"os"
	"path/filepath"
	"reflect"
	"runtime"
	"testing"
	"time"
)

func TestClientDrivesPMMPRuntimeProcess(t *testing.T) {
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	root := packageRoot(t)
	pluginsDir := t.TempDir()
	writeEchoPlugin(t, pluginsDir)

	client, err := Start(ctx, "php", filepath.Join(root, "bin", "pmmpcompat-runtime.php"), pluginsDir)
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

	join, actions, err := client.PlayerJoin(ctx, "u1", "Steve")
	if err != nil {
		t.Fatalf("join: %v", err)
	}
	if join.Player.Name != "Steve" || join.JoinMessage != "Steve joined the game" {
		t.Fatalf("join result = %#v", join)
	}
	wantActions := []Action{{Type: "player.send_message", UUID: "u1", Message: "welcome Steve"}}
	if len(actions) != 3 {
		t.Fatalf("join actions = %#v", actions)
	}
	if !reflect.DeepEqual(actions[:1], wantActions) {
		t.Fatalf("join message action = %#v, want %#v", actions[:1], wantActions)
	}
	if actions[1].Type != "player.send_form" || actions[1].UUID != "u1" || actions[1].FormID != 1 {
		t.Fatalf("join form action = %#v", actions[1])
	}
	if actions[2].Type != "player.inventory.set_item" || actions[2].UUID != "u1" || actions[2].Slot != 0 {
		t.Fatalf("join inventory action = %#v", actions[2])
	}
	if len(actions[2].Item) == 0 {
		t.Fatalf("join inventory item payload is empty: %#v", actions[2])
	}

	form, actions, err := client.FormResponse(ctx, "u1", 1, 0)
	if err != nil {
		t.Fatalf("form response: %v", err)
	}
	if !form.Handled {
		t.Fatalf("form response result = %#v", form)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "u1", Message: "form response 0"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("form response actions = %#v, want %#v", actions, wantActions)
	}

	inventory, actions, err := client.PlayerInventory(ctx, "u1", []InventorySlot{{
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

	command, actions, err := client.Command(ctx, "u1", "Steve", "slot", []string{"5"})
	if err != nil {
		t.Fatalf("slot command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("slot command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "u1", Message: "slot 5 minecraft:diamond 7"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("slot command actions = %#v, want %#v", actions, wantActions)
	}

	state, actions, err := client.PlayerState(ctx, "u1", PlayerState{
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

	command, actions, err = client.Command(ctx, "u1", "Steve", "state", nil)
	if err != nil {
		t.Fatalf("state command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("state command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "u1", Message: "state arena 10.5 64 -3.25 14.5/30 Adventure 9 0.75"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("state command actions = %#v, want %#v", actions, wantActions)
	}

	command, actions, err = client.Command(ctx, "u1", "Steve", "mutate", nil)
	if err != nil {
		t.Fatalf("mutate command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("mutate command result = %#v", command)
	}
	wantActions = []Action{
		{Type: "player.set_health", UUID: "u1", Health: 6, MaxHealth: 30},
		{Type: "player.set_gamemode", UUID: "u1", Gamemode: "creative"},
		{Type: "player.set_experience", UUID: "u1", XPLevel: 12, XPProgress: 0.75},
		{Type: "player.set_experience", UUID: "u1", XPLevel: 12, XPProgress: 0.5},
		{Type: "player.teleport", UUID: "u1", Position: &Position{X: 1, Y: 2, Z: 3, World: "mutated"}},
		{Type: "player.send_popup", UUID: "u1", Message: "popup"},
		{Type: "player.send_tip", UUID: "u1", Message: "tip"},
		{Type: "player.send_actionbar", UUID: "u1", Message: "actionbar"},
		{Type: "player.send_title", UUID: "u1", Title: "title", Subtitle: "subtitle"},
		{Type: "player.set_title_duration", UUID: "u1", FadeIn: 5, Stay: 40, FadeOut: 5},
		{Type: "player.reset_titles", UUID: "u1"},
		{Type: "player.remove_titles", UUID: "u1"},
		{Type: "player.set_allow_flight", UUID: "u1", Value: boolPtr(true)},
		{Type: "player.set_flying", UUID: "u1", Value: boolPtr(true)},
		{Type: "player.set_flight_speed", UUID: "u1", Speed: 0.2},
		{Type: "player.set_view_distance", UUID: "u1", Distance: 8},
		{Type: "player.transfer", UUID: "u1", Address: "127.0.0.1", Port: 19133, Message: "switching"},
	}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("mutate command actions = %#v, want %#v", actions, wantActions)
	}

	chat, actions, err := client.Chat(ctx, "u1", "Steve", "hello")
	if err != nil {
		t.Fatalf("chat: %v", err)
	}
	if chat.Message != "HELLO" || chat.Cancelled {
		t.Fatalf("chat result = %#v", chat)
	}
	if len(actions) != 0 {
		t.Fatalf("chat actions = %#v", actions)
	}

	command, actions, err = client.Command(ctx, "u1", "Steve", "echo", []string{"from", "go"})
	if err != nil {
		t.Fatalf("command: %v", err)
	}
	if !command.Handled {
		t.Fatalf("command result = %#v", command)
	}
	wantActions = []Action{{Type: "player.send_message", UUID: "u1", Message: "echo from go"}}
	if !reflect.DeepEqual(actions, wantActions) {
		t.Fatalf("command actions = %#v, want %#v", actions, wantActions)
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
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
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
