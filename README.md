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
- Events: join, quit, chat, command preprocess, move, block break, block place, item use/drop/interact, entity damage.
- Directory plugin loading sorted by `depend`, `softdepend`, and `loadbefore`.
- Transport-neutral `pocketmine\compat\Runtime` for host adapters to drive joins, quits, chat, commands, movement, block break/place, and scheduler ticks.
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
php -d phar.readonly=0 tools/phar-smoke.php
```

The smoke tests create temporary PMMP-style plugin folders and phars, load them via `PluginLoader` and the JSON-lines runtime process, validate dependency order, run lifecycle hooks, copy bundled resources/configs, dispatch command aliases, dispatch listener events, exercise forms/common events including form response callbacks, run scheduler ticks, persist configs and SQLite state, run local async task completion, exercise bundled virion-style classes, and verify bridge actions back to the host.

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
```

Lunar/Dragonfly code should integrate at this layer: keep the PHP process alive, forward normalized host events, and apply returned action records. The Go package includes `ApplyActions()`, `TargetResolver`, `PlayerTarget`, and `ServerTarget` so host code can map every emitted action through typed methods instead of switching on raw action strings in gameplay code.

`host/dragonfly` contains the first concrete Dragonfly adapter module. It wraps Dragonfly players/servers as `PlayerTarget`/`ServerTarget`, maps common message/title/teleport/kick/transfer/gamemode/XP/flying/inventory-clear actions directly, and exposes extension callbacks for host-specific forms, allow-flight ability policy, exact health, item, and view-distance handling.

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
