# pmmpcompat

`pmmpcompat` is a PocketMine-MP compatibility facade for running simple drag-and-drop PMMP-style PHP plugins on a Bedrock server host.

The package intentionally exposes only `pocketmine\...` classes to plugin code. It is not based on `bedrock-mc/plugin`, protobuf plugin APIs, or generated transport classes.

## Current Supported Surface

- Plugin folders or `.phar` archives with `plugin.yml` and `src/`.
- `plugin.yml` parsing for scalar fields, nested maps, inline lists, and common block-list fields such as `api`, `depend`, `softdepend`, `loadbefore`, and command `aliases`.
- `pocketmine\plugin\PluginBase` lifecycle: `onLoad()`, `onEnable()`, `onDisable()`.
- Basic `PluginDescription`, data folder, logger, config, and plugin scheduler.
- `Server`, console sender, permission manager, online player registry, broadcast, command map.
- `Player` wrapper with message, popup/tip/actionbar/title, permissions, inventory, world/position, teleport, transfer, kick, flight/view settings, and optional transport-neutral bridge callbacks.
- Form facade: `SimpleForm`, `ModalForm`, `CustomForm`, `Player::sendForm()` capture.
- PMMP-style command registration from `plugin.yml`, aliases, permission checks, and `onCommand()`.
- Reflected listener registration through `PluginManager::registerEvents()`.
- Events: join, quit, chat, command preprocess/command, move, block break, block place, item use/drop/interact, entity damage, death, respawn.
- Directory plugin loading sorted by `depend`, `softdepend`, and `loadbefore`.
- Transport-neutral `pocketmine\compat\Runtime` for host adapters to drive joins, quits, chat, commands, movement, block break/place/interact, damage, death, respawn, forms, inventory/state sync, and scheduler ticks.
- Simple `Config`, `TaskScheduler`, `ClosureTask`, `Vector3`, `Position`, `World`, `Block`, `Item`, `Inventory`, permissions.

The facade is still intentionally Dragonfly-hosted, not a PocketMine server process, but the broad PMMP API shape is now present: the checked-in audit has zero missing public classes and zero missing public members against the local PMMP reference. Remaining work is about depth and fidelity of host mappings, generated Bedrock registry coverage, and real plugin corpus validation, not generated placeholder classes.

## Compatibility Audit

Do not expand this facade by guessing the whole PMMP API by hand. Use the upstream reference audit first:

```bash
php tools/api-audit.php
```

The checked-in [API_AUDIT.json](API_AUDIT.json) records the current gap between `refs/pocketmine/PocketMine-MP/src` and this facade. Implement missing classes/methods because a real plugin needs them, then update tests and regenerate the audit.

## Smoke Test

From this directory:

```bash
php tools/smoke.php
php tools/plugin-corpus-smoke.php
php tools/ipc-smoke.php
php tools/bridge-action-audit.php
php -d phar.readonly=0 tools/plugin-package-corpus.php --self-test
php -d phar.readonly=0 tools/phar-smoke.php
```

For production-shaped validation, use the PocketMine PHP build instead of stock system PHP. Download the matching archive from [pmmp/PHP-Binaries `pm5-php-8.2-latest`](https://github.com/pmmp/PHP-Binaries/releases/tag/pm5-php-8.2-latest), extract it, then point `PMMPCOMPAT_PHP` at its `php` executable:

```bash
export PMMPCOMPAT_PHP=/path/to/pmmp-php/bin/php7/bin/php
export PMMPCOMPAT_PHP_ARGS="-d extension_dir=/path/to/pmmp-php/bin/php7/lib/php/extensions/no-debug-zts-20220829"
$PMMPCOMPAT_PHP $PMMPCOMPAT_PHP_ARGS -d phar.readonly=0 tools/plugin-package-corpus.php /path/to/Plugin.phar
php tools/ipc-smoke.php
```

If `PMMPCOMPAT_PHP` is not set, tests and tools use the current PHP runtime. `PMMPCOMPAT_PHP_ARGS` is optional, but it is useful for relocated PMMP PHP archives whose `php.ini` still points at the build-machine extension directory. The checked-in fallback shims let local validation run on stock PHP, but native PMMP PHP is the intended path for real async/thread fidelity because it can provide PMMP's `pmmp\thread` support.

The smoke tests create temporary PMMP-style plugin folders and phars, load them via `PluginLoader` and the JSON-lines runtime process, validate dependency order, run lifecycle hooks, copy bundled resources/configs, dispatch command aliases, dispatch listener events, exercise forms/common events including form response callbacks, run scheduler ticks, persist configs and SQLite state, run local async task completion, exercise bundled virion-style classes, and verify bridge actions back to the host.

For outside plugin packages, run `php -d phar.readonly=0 tools/plugin-package-corpus.php <path> [...]` with plugin folders, `.phar` files, or directories containing multiple dropped plugins. The tool copies each corpus into an isolated runtime, then verifies load, enable, ticks, disable, and emitted action collection.

For early experiments without Composer, require the package autoloader:

```php
require __DIR__ . "/autoload.php";
```

See [ARCHITECTURE.md](ARCHITECTURE.md) for the host adapter, bridge, reflection, and memory-boundary design.

## Go Host Client

The Go adapter in `host/go` drives the PHP runtime process over JSON lines:

```bash
cd host/go
go test ./...
cd ../dragonfly
go test ./...
```

Set `PMMPCOMPAT_PHP=/path/to/pmmp-php/bin/php7/bin/php` and, when needed, `PMMPCOMPAT_PHP_ARGS="-d extension_dir=/path/to/pmmp-php/bin/php7/lib/php/extensions/no-debug-zts-20220829"` before running the Go tests to make the host client spawn the same PHP binary the server will use. Go hosts that need explicit PHP flags can call `StartWithArgs()`.

Lunar/Dragonfly code should integrate at this layer: keep the PHP process alive, forward normalized host events, and apply returned action records. The Go package includes `ApplyActions()`, `TargetResolver`, `PlayerTarget`, and `ServerTarget` so host code can map every emitted action through typed methods instead of switching on raw action strings in gameplay code.

`host/dragonfly` contains the first concrete Dragonfly adapter module. It wraps Dragonfly players/servers as `PlayerTarget`/`ServerTarget`, maps common message/title/teleport/kick/transfer/gamemode/XP/flying/inventory-clear actions directly, preserves PMMP form JSON through `Runtime.FormMapper()`, registers Dragonfly command stubs for PMMP plugin commands through `Runtime.RegisterCommands()`, and exposes extension callbacks for allow-flight ability policy, exact health, item, world lookup, and view-distance handling.

Minimal Dragonfly integration shape:

```go
client, err := pmmpcompat.StartWithArgs(ctx, phpBinary, phpArgs, runtimeScript, pluginsDir)
if err != nil {
    return err
}

rt := dragonfly.NewRuntime(client, srv, dragonfly.RuntimeOptions{
    Options: dragonfly.Options{
        ItemMapper: dragonfly.DefaultItemMapper,
        HealthSetter: dragonfly.EventedHealthSetter,
        AllowFlightSetter: yourAllowFlightSetter,
        ViewDistanceSetter: yourViewDistanceSetter,
    },
    WorldLookup: yourWorldLookup,
})

if _, _, err := client.Load(ctx); err != nil { return err }
if _, err := client.Enable(ctx); err != nil { return err }
if err := rt.RegisterCommands(ctx); err != nil { return err }

// In the accept loop, after Dragonfly has spawned the player:
h, err := rt.RegisterPlayer(ctx, p)
if err != nil {
    p.Disconnect("PocketMine runtime error")
    return
}
p.Handle(h)
```

The Dragonfly handler forwards movement, chat, PMMP command stubs, block break/place, block interact, damage, death, respawn, quit, form response, inventory sync, and player state sync into the PHP runtime, then applies returned PMMP bridge actions back to Dragonfly. Existing PMMP dependencies, virions, and soft dependencies should still be supplied with the plugin package; this library does not vendor or reimplement plugin-specific dependencies.

## Live Dragonfly Example

`cmd/example` is a runnable Dragonfly server wired to the PMMP compatibility runtime. Drop PMMP plugin folders or `.phar` files into `cmd/example/plugins`, then run:

```bash
cd cmd/example
./start.sh
```

On Windows PowerShell:

```powershell
cd cmd/example
.\start.ps1
```

`start.sh` and `start.ps1` download the current PMMP PHP 8.2 binary from [pmmp/PHP-Binaries `pm5-php-8.2-latest`](https://github.com/pmmp/PHP-Binaries/releases/tag/pm5-php-8.2-latest) when the local PMMP PHP binary is missing. PMMP still extracts the runtime under `bin/php7` even for PHP 8.x builds. The scripts set `PMMPCOMPAT_PHP`, `PMMPCOMPAT_PHP_ARGS`, `PMMPCOMPAT_PLUGINS`, and `PMMPCOMPAT_DATA`, then run the Go example server. The example modules intentionally do not use local `replace` directives, so a fresh clone resolves pushed upstream module versions directly.

Useful overrides:

```bash
PMMPCOMPAT_PHP=/path/to/bin/php7/bin/php ./start.sh
PMMPCOMPAT_PLUGINS=/path/to/plugins ./start.sh -addr :19133
PMMPCOMPAT_ONLINE_AUTH=true ./start.sh
```

PowerShell equivalents:

```powershell
$env:PMMPCOMPAT_PHP = "C:\path\to\bin\php7\php.exe"
$env:PMMPCOMPAT_PLUGINS = "C:\path\to\plugins"
.\start.ps1 -addr :19133
```

By default the server listens on `:19132` with offline auth enabled for local testing. It loads/enables PMMP plugins before starting Dragonfly, registers PMMP commands as Dragonfly command stubs, bridges player joins/chat/move/block/interact/damage/death/respawn/form events, and ticks the PMMP scheduler every 50ms.

## Runtime Direction

The intended production shape is:

```text
Dragonfly host
  -> host-specific adapter process
    -> pmmpcompat Runtime
      -> pocketmine\... compatibility facade
      -> drag-and-drop PMMP plugin folders/phars
```

Host adapters may use any private transport they want, but this package stays transport-neutral. PMMP plugin authors and the facade itself should not see protobuf SDK classes.
