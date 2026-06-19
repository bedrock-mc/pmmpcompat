# pmmpcompat Architecture

`pmmpcompat` is the PHP compatibility layer. It does not run a PocketMine server and it does not expose a protobuf/native plugin API to PMMP plugins.

Plugin metadata is read from PMMP-style `plugin.yml` files. The built-in parser intentionally covers the common no-dependency subset needed for plugin loading: scalar fields, nested maps, inline lists, and block lists for API versions, dependencies, load ordering, command aliases, and permissions.

This is not a hand rewrite of the full PMMP server. The facade is guided by `tools/api-audit.php`, which compares public PMMP reference classes/methods/constants against this package. The audit is currently expected to report zero missing public classes and members. Compatibility work should now focus on behavior depth, host bridge fidelity, real plugin corpus coverage, and richer Bedrock registry data rather than adding generated placeholders.

## Mapping Policy

Every PMMP-facing method must fall into one of three implementation categories:

- **Local runtime compatibility:** Pure PHP/plugin-runtime state with no Dragonfly equivalent, such as `plugin.yml`, `PluginDescription`, config files, command metadata, listener reflection, form callbacks, and scheduler bookkeeping.
- **Host-backed mapping:** State owned by Dragonfly/Lunar, such as player inventory, player movement, teleport, kick, messages, forms, world/block changes, and eventually item/block state. These methods may maintain a PHP mirror so plugin code can read back values, but mutations must emit bridge actions for the Go host to apply.
- **Explicit unsupported:** APIs with no safe current mapping should fail clearly instead of pretending full PMMP behavior exists.

Do not expand local mirrors for host-owned state as the final behavior. Add a bridge action or query shape first, then keep only the PHP state needed for source compatibility and immediate plugin reads.

## Boundaries

```text
Go/Dragonfly/Lunar host
  -> host adapter
    -> pocketmine\compat\Runtime
      -> pocketmine\... facade
        -> drag-and-drop PMMP plugin PHP code
```

The host adapter may be Go, PHP, IPC-backed, embedded, or process-based. That adapter is outside this package. Its job is to translate host events into plain `Runtime` method calls and translate bridge callbacks back into host actions.

## Host To PMMP

Host code should not ask PMMP plugins to handle protobuf or native host structs. It should pass normalized values into `Runtime`:

- `playerJoin($uuid, $name, $bridge)`
- `playerQuit($uuid, $name)`
- `chat($uuid, $name, $message)`
- `command($uuid, $name, $command, $args)`
- `playerMove($uuid, $name, $to)`
- `blockBreak($uuid, $name, $position)`
- `blockPlace($uuid, $name, $position)`
- `tick($currentTick)`
- `formResponse($uuid, $formId, $data)`
- `syncPlayerInventory($uuid, $slots)`
- `syncPlayerState($uuid, $state)`

`Runtime` converts those values into PMMP-style objects and events such as `PlayerJoinEvent`, `PlayerChatEvent`, `PlayerCommandPreprocessEvent`, and `BlockBreakEvent`.

Host snapshots, such as inventory and player-state sync, update only the PHP compatibility mirror and must not emit host actions back to Dragonfly. Dragonfly remains authoritative for player/world state.

Local compatibility subsystems such as `Server::getAsyncPool()`, `Server::getWorldManager()`, config files, SQLite usage from plugins, bundled dependency/virion classes, and scheduler ticks run inside PHP. They are local runtime compatibility unless they emit an explicit host action.

## PMMP To Host

PMMP plugins call familiar APIs like:

- `$player->sendMessage(...)`
- `$player->sendPopup(...)`, `$player->sendTip(...)`, `$player->sendActionBarMessage(...)`
- `$player->sendTitle(...)`, `$player->setTitleDuration(...)`, `$player->resetTitles()`
- `$player->teleport($position)`
- `$player->transfer(...)`
- `$player->kick(...)`
- `$player->sendForm($form)`
- `$player->setHealth(...)`
- `$player->setGamemode(...)`
- `$player->setXpLevel(...)`
- `$player->setXpProgress(...)`
- `$player->setAllowFlight(...)`, `$player->setFlying(...)`, `$player->setFlightSpeedMultiplier(...)`
- `$player->setViewDistance(...)`
- `$player->getInventory()->setItem(...)`
- `$player->getInventory()->addItem(...)`
- `$server->broadcastMessage(...)`

Those calls are captured by the facade and optionally forwarded through local bridge interfaces:

- `pocketmine\compat\PlayerBridge`
- `pocketmine\compat\ServerBridge`

The host adapter implements those bridge interfaces and maps them back to whatever native host action mechanism it uses. Forms are assigned stable per-player IDs; a `player.send_form` action includes `form_id`, and the host returns the client answer with a `form_response` request.

Inventory actions are emitted as `player.inventory.set_item`, `player.inventory.clear_slot`, and `player.inventory.clear`. A Dragonfly adapter should apply those to the native player inventory inside the correct world/server context, not treat the PHP inventory mirror as authoritative.

Player-state actions are emitted as `player.set_health`, `player.set_gamemode`, `player.set_experience`, `player.teleport`, `player.transfer`, `player.set_allow_flight`, `player.set_flying`, `player.set_flight_speed`, and `player.set_view_distance`. Visual actions such as `player.send_popup`, `player.send_tip`, `player.send_actionbar`, `player.send_title`, `player.set_title_duration`, `player.reset_titles`, and `player.remove_titles` should be applied through the host's normal player packet/UI path. A Dragonfly adapter should apply state/world changes on the owning player/world goroutine or transaction boundary required by Dragonfly.

## Reflection

Reflection stays entirely inside the PHP PMMP facade. For example, `PluginManager::registerEvents()` reflects listener methods and their `pocketmine\event\...` type hints, just like PMMP-style plugin code expects.

The Go/Dragonfly/Lunar side does not reflect PHP plugin classes. It only sends normalized event payloads to `Runtime` and receives bridge actions from `PlayerBridge`/`ServerBridge`.

## Memory Model

There is no shared Go/PHP object memory contract. Host objects should be represented by stable IDs and facade wrappers:

- Player identity: UUID/name.
- World/block/vector values: simple PHP value objects.
- Host actions: bridge callbacks.
- PMMP plugin objects: PHP-only.

If the runtime is split into a separate PHP process, these same boundaries can be serialized over IPC without changing plugin-facing APIs.

## JSON-Lines Runtime

`bin/pmmpcompat-runtime.php` is a reference host process. It reads one JSON request per line from STDIN and writes one JSON response per line to STDOUT.

Example request:

```json
{"id":1,"type":"player_join","payload":{"uuid":"u1","name":"Steve"}}
```

Example response:

```json
{"id":1,"ok":true,"result":{"player":{"uuid":"u1","name":"Steve"},"join_message":"Steve joined the game"},"actions":[{"type":"player.send_message","uuid":"u1","message":"welcome Steve"},{"type":"player.send_form","uuid":"u1","form_id":1,"form":{"type":"form","title":"Menu","content":"Pick","buttons":[{"text":"Start"}]}}]}
```

Example form response request:

```json
{"id":2,"type":"form_response","payload":{"uuid":"u1","form_id":1,"data":0}}
```

This process is intentionally a plain IPC adapter surface. A Go host can keep the PHP process alive, write host events as JSON lines, apply returned action records, and avoid exposing native host objects to PMMP plugin code.

## Go Host Adapter

`host/go` contains a small client for native host code. It starts the PHP runtime process, sends JSON-lines requests, decodes responses, and exposes returned host actions as Go structs.

The Go package also provides an action application contract:

- `ApplyActions(ctx, resolver, actions)` applies a batch in order.
- `TargetResolver` locates host player/server targets by UUID.
- `PlayerTarget` and `ServerTarget` enumerate every currently emitted bridge action.

Dragonfly/Lunar adapters should implement those interfaces around their native player/server handles. World and player mutations must still be scheduled through Dragonfly's correct goroutine or transaction boundary; `pmmpcompat` only normalizes and dispatches the intent.

`host/dragonfly` is a separate Go module for concrete Dragonfly hosts. It imports Dragonfly and the transport-neutral host client, then adapts Dragonfly `*player.Player` and `*server.Server` values to the typed target interfaces. Generic actions are applied directly. PMMP form JSON can be wrapped with `RawFormMapper`, which preserves plugin-authored payloads as Dragonfly `form.Form` values and reports raw submit JSON back to the embedding host. Host-specific conversion points, such as allow-flight ability policy, exact health assignment, and PMMP item JSON to Dragonfly `item.Stack`, remain explicit callbacks so the adapter cannot silently invent lossy behavior.

This is the intended integration shape for Lunar/Dragonfly work:

1. Dragonfly event handler receives a native event.
2. Lunar adapter normalizes it into a `pmmpcompat.Client` call such as `Chat()` or `BlockBreak()`.
3. The PHP runtime dispatches PMMP events/listeners.
4. The Go client receives action records such as `player.send_message`, `player.teleport`, or `server.broadcast_message`.
5. Lunar adapter applies those actions through the typed `PlayerTarget`/`ServerTarget` interfaces inside the correct server/world context.
