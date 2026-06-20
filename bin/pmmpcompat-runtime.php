#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\compat\HostActionQueue;
use pocketmine\compat\Runtime;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;

ini_set('display_errors', 'stderr');
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }
    fwrite(STDERR, errorSeverityName($severity) . ": {$message} in {$file} on line {$line}\n");
    return true;
});
ob_start();

$pluginsDir = $argv[1] ?? getcwd() . DIRECTORY_SEPARATOR . 'plugins';
$queue = new HostActionQueue();
$runtime = new Runtime($pluginsDir, new Server($queue));

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $request = json_decode($line, true);
    if (!is_array($request)) {
        fwrite(STDOUT, json_encode(['ok' => false, 'error' => 'invalid_json']) . "\n");
        fflush(STDOUT);
        continue;
    }
    $id = $request['id'] ?? null;
    try {
        $result = handleRequest($runtime, $queue, $request);
        flushRuntimeOutput();
        fwrite(STDOUT, json_encode(['id' => $id, 'ok' => true, 'result' => $result, 'actions' => $queue->drain()]) . "\n");
    } catch (Throwable $e) {
        flushRuntimeOutput();
        fwrite(STDOUT, json_encode(['id' => $id, 'ok' => false, 'error' => $e->getMessage(), 'actions' => $queue->drain()]) . "\n");
    }
    fflush(STDOUT);
}

try {
    $runtime->shutdown();
    flushRuntimeOutput();
} catch (Throwable $e) {
    fwrite(STDERR, "Runtime shutdown failed: {$e->getMessage()}\n");
}

/** @param array<string, mixed> $request @return array<string, mixed> */
function handleRequest(Runtime $runtime, HostActionQueue $queue, array $request): array
{
    $type = (string) ($request['type'] ?? '');
    $payload = is_array($request['payload'] ?? null) ? $request['payload'] : [];
    return match ($type) {
        'load' => resultOf(static function () use ($runtime): array {
            $runtime->load();
            return ['plugins' => array_map(static fn($plugin): string => $plugin->getName(), $runtime->plugins())];
        }),
        'enable' => resultOf(static function () use ($runtime): array {
            $runtime->enable();
            return ['enabled' => true];
        }),
        'commands' => commands($runtime),
        'disable' => resultOf(static function () use ($runtime): array {
            $runtime->disable();
            return ['disabled' => true];
        }),
        'player_join' => playerJoin($runtime, $queue, $payload),
        'player_quit' => playerQuit($runtime, $payload),
        'chat' => chat($runtime, $payload),
        'command' => command($runtime, $payload),
        'player_move' => playerMove($runtime, $payload),
        'entity_damage' => entityDamage($runtime, $payload),
        'player_death' => playerDeath($runtime, $payload),
        'player_respawn' => playerRespawn($runtime, $payload),
        'block_break' => blockBreak($runtime, $payload),
        'block_place' => blockPlace($runtime, $payload),
        'player_interact' => playerInteract($runtime, $payload),
        'player_inventory' => playerInventory($runtime, $payload),
        'player_state' => playerState($runtime, $payload),
        'form_response' => formResponse($runtime, $payload),
        'tick' => resultOf(static function () use ($runtime, $payload): array {
            $runtime->tick(intValue($payload, 'tick'));
            return ['tick' => intValue($payload, 'tick')];
        }),
        'drain_actions' => [],
        default => throw new RuntimeException("Unknown runtime request type: {$type}"),
    };
}

/** @return array<string, mixed> */
function commands(Runtime $runtime): array
{
    $seen = [];
    $commands = [];
    foreach ($runtime->server()->getCommandMap()->getCommands() as $label => $command) {
        $id = spl_object_id($command);
        if (isset($seen[$id])) {
            continue;
        }
        $seen[$id] = true;
        $commands[] = [
            'name' => $command->getName(),
            'description' => $command->getDescription(),
            'aliases' => $command->getAliases(),
            'permission' => $command->getPermission(),
            'usage' => $command->getUsage(),
            'overloads' => commandOverloads($command),
        ];
    }
    return ['commands' => $commands];
}

/** @return array<int, array<string, mixed>> */
function commandOverloads(object $command): array
{
    if (!method_exists($command, 'getSubCommands') && !method_exists($command, 'getArgumentList')) {
        return [];
    }
    return argumentableOverloads($command);
}

/** @return array<int, array<string, mixed>> */
function argumentableOverloads(object $argumentable): array
{
    $overloads = [];
    if (method_exists($argumentable, 'getSubCommands')) {
        foreach ($argumentable->getSubCommands() as $label => $subCommand) {
            if (!is_object($subCommand) || !method_exists($subCommand, 'getName') || $subCommand->getName() !== $label) {
                continue;
            }
            $subcommandParam = [
                'name' => (string) $label,
                'type' => \pocketmine\network\mcpe\protocol\AvailableCommandsPacket::ARG_FLAG_VALID | \pocketmine\network\mcpe\protocol\AvailableCommandsPacket::ARG_FLAG_ENUM,
                'type_name' => 'subcommand',
                'optional' => false,
                'enum_name' => (string) $label,
                'enum_values' => [(string) $label],
                'subcommand' => true,
            ];
            $childOverloads = argumentableOverloads($subCommand);
            if ($childOverloads === []) {
                $overloads[] = ['parameters' => [$subcommandParam]];
                continue;
            }
            foreach ($childOverloads as $childOverload) {
                $parameters = $childOverload['parameters'] ?? [];
                array_unshift($parameters, $subcommandParam);
                $overloads[] = ['parameters' => $parameters];
            }
        }
    }
    foreach (argumentOverloadCombinations($argumentable) as $parameters) {
        $overloads[] = ['parameters' => $parameters];
    }
    return $overloads;
}

/** @return array<int, array<int, array<string, mixed>>> */
function argumentOverloadCombinations(object $argumentable): array
{
    if (!method_exists($argumentable, 'getArgumentList')) {
        return [];
    }
    $input = $argumentable->getArgumentList();
    if (!is_array($input) || $input === []) {
        return [];
    }
    $positions = [];
    foreach ($input as $positionArguments) {
        if (!is_array($positionArguments) || $positionArguments === []) {
            continue;
        }
        $choices = [];
        foreach ($positionArguments as $argument) {
            if (is_object($argument)) {
                $choices[] = commandParameterPayload($argument);
            }
        }
        if ($choices !== []) {
            $positions[] = $choices;
        }
    }
    if ($positions === []) {
        return [];
    }
    $combinations = [[]];
    foreach ($positions as $choices) {
        $next = [];
        foreach ($combinations as $prefix) {
            foreach ($choices as $choice) {
                $next[] = [...$prefix, $choice];
            }
        }
        $combinations = $next;
    }
    return $combinations;
}

/** @return array<string, mixed> */
function commandParameterPayload(object $argument): array
{
    $name = method_exists($argument, 'getName') ? (string) $argument->getName() : 'value';
    $typeName = method_exists($argument, 'getTypeName') ? (string) $argument->getTypeName() : '';
    $optional = method_exists($argument, 'isOptional') && (bool) $argument->isOptional();
    $type = 0;
    $enumName = '';
    $enumValues = [];
    if (method_exists($argument, 'getNetworkParameterData')) {
        $parameter = $argument->getNetworkParameterData();
        if (is_object($parameter)) {
            $name = isset($parameter->paramName) ? (string) $parameter->paramName : $name;
            $type = isset($parameter->paramType) ? (int) $parameter->paramType : $type;
            $optional = isset($parameter->isOptional) ? (bool) $parameter->isOptional : $optional;
            $enum = $parameter->enum ?? null;
            if (is_object($enum)) {
                $enumName = isset($enum->enumName) ? (string) $enum->enumName : '';
                $values = $enum->values ?? [];
                if (is_array($values)) {
                    $enumValues = array_values(array_map('strval', $values));
                }
            }
        }
    }
    return [
        'name' => $name,
        'type' => $type,
        'type_name' => $typeName,
        'optional' => $optional,
        'enum_name' => $enumName,
        'enum_values' => $enumValues,
        'subcommand' => false,
    ];
}

/** @return array<string, mixed> */
function playerJoin(Runtime $runtime, HostActionQueue $queue, array $payload): array
{
    $uuid = stringValue($payload, 'uuid');
    $state = playerStatePayload($payload);
    $slots = isset($payload['slots']) && is_array($payload['slots']) ? inventorySlots($payload['slots']) : [];
    $event = $runtime->playerJoin($uuid, stringValue($payload, 'name'), $queue->forPlayer($uuid), $state, $slots);
    return ['player' => playerPayload($uuid, $event->getPlayer()->getName()), 'join_message' => $event->getJoinMessage()];
}

/** @return array<string, mixed> */
function playerQuit(Runtime $runtime, array $payload): array
{
    $uuid = stringValue($payload, 'uuid');
    $event = $runtime->playerQuit($uuid, stringValue($payload, 'name'));
    return ['player' => playerPayload($uuid, $event->getPlayer()->getName()), 'quit_message' => $event->getQuitMessage()];
}

/** @return array<string, mixed> */
function chat(Runtime $runtime, array $payload): array
{
    $event = $runtime->chat(stringValue($payload, 'uuid'), stringValue($payload, 'name'), stringValue($payload, 'message'));
    return [
        'cancelled' => $event->isCancelled(),
        'message' => $event->getMessage(),
        'formatted_message' => messageText($event->getFormatter()->format($event->getPlayer()->getDisplayName(), $event->getMessage())),
        'recipient_count' => count($event->getRecipients()),
    ];
}

/** @return array<string, mixed> */
function command(Runtime $runtime, array $payload): array
{
    $args = $payload['args'] ?? [];
    if (!is_array($args)) {
        $args = [];
    }
    return ['handled' => $runtime->command(stringValue($payload, 'uuid'), stringValue($payload, 'name'), stringValue($payload, 'command'), array_map('strval', $args))];
}

/** @return array<string, mixed> */
function playerMove(Runtime $runtime, array $payload): array
{
    $event = $runtime->playerMove(stringValue($payload, 'uuid'), stringValue($payload, 'name'), positionValue($payload, 'to'));
    return ['cancelled' => $event->isCancelled(), 'from' => positionPayload($event->getFrom()), 'to' => positionPayload($event->getTo())];
}

/** @return array<string, mixed> */
function entityDamage(Runtime $runtime, array $payload): array
{
    $event = $runtime->entityDamage(
        stringValue($payload, 'uuid'),
        stringValue($payload, 'name'),
        floatValue($payload, 'base_damage'),
        isset($payload['cause']) && is_numeric($payload['cause']) ? (int) $payload['cause'] : EntityDamageEvent::CAUSE_CUSTOM,
        isset($payload['damager_uuid']) && is_scalar($payload['damager_uuid']) ? (string) $payload['damager_uuid'] : null,
        isset($payload['damager_name']) && is_scalar($payload['damager_name']) ? (string) $payload['damager_name'] : null,
    );
    $result = [
        'cancelled' => $event->isCancelled(),
        'base_damage' => $event->getBaseDamage(),
        'final_damage' => $event->getFinalDamage(),
        'cause' => $event->getCause(),
    ];
    if ($event instanceof \pocketmine\event\entity\EntityDamageByEntityEvent) {
        $damager = $event->getDamager();
        $result['damager'] = $damager instanceof \pocketmine\player\Player ? playerPayload($damager->getUniqueId()->toString(), $damager->getName()) : null;
    }
    return $result;
}

/** @return array<string, mixed> */
function playerDeath(Runtime $runtime, array $payload): array
{
    $event = $runtime->playerDeath(stringValue($payload, 'uuid'), stringValue($payload, 'name'), [], intValueDefault($payload, 'xp', 0), isset($payload['message']) && is_scalar($payload['message']) ? (string) $payload['message'] : null);
    return [
        'death_message' => messageText($event->getDeathMessage()),
        'death_screen_message' => messageText($event->getDeathScreenMessage()),
        'keep_inventory' => $event->getKeepInventory(),
        'keep_xp' => $event->getKeepXp(),
        'xp' => $event->getXpDropAmount(),
    ];
}

/** @return array<string, mixed> */
function playerRespawn(Runtime $runtime, array $payload): array
{
    $position = isset($payload['position']) && is_array($payload['position']) ? positionValue($payload, 'position') : null;
    $event = $runtime->playerRespawn(stringValue($payload, 'uuid'), stringValue($payload, 'name'), $position);
    return ['position' => positionPayload($event->getRespawnPosition())];
}

/** @return array<string, mixed> */
function blockBreak(Runtime $runtime, array $payload): array
{
    $event = $runtime->blockBreak(stringValue($payload, 'uuid'), stringValue($payload, 'name'), vectorValue($payload, 'position'), optionalBlock($payload), optionalItem($payload));
    return ['cancelled' => $event->isCancelled(), 'position' => vectorPayload($event->getBlockPosition()), 'block' => blockPayload($event->getBlock())];
}

/** @return array<string, mixed> */
function blockPlace(Runtime $runtime, array $payload): array
{
    $event = $runtime->blockPlace(stringValue($payload, 'uuid'), stringValue($payload, 'name'), vectorValue($payload, 'position'), optionalBlock($payload), optionalItem($payload));
    return ['cancelled' => $event->isCancelled(), 'position' => vectorPayload($event->getBlockPosition()), 'blocks' => transactionPayload($event->getTransaction())];
}

/** @return array<string, mixed> */
function playerInteract(Runtime $runtime, array $payload): array
{
    $event = $runtime->playerInteract(
        stringValue($payload, 'uuid'),
        stringValue($payload, 'name'),
        vectorValue($payload, 'position'),
        isset($payload['action']) && is_numeric($payload['action']) ? (int) $payload['action'] : \pocketmine\event\player\PlayerInteractEvent::RIGHT_CLICK_BLOCK,
        optionalBlock($payload),
        optionalItem($payload),
        isset($payload['touch_vector']) && is_array($payload['touch_vector']) ? vectorValue($payload, 'touch_vector') : null,
        isset($payload['face']) && is_numeric($payload['face']) ? (int) $payload['face'] : null,
    );
    return [
        'cancelled' => $event->isCancelled(),
        'position' => $event->getBlock() !== null ? vectorPayload($event->getBlock()->getPosition() ?? vectorValue($payload, 'position')) : vectorPayload(vectorValue($payload, 'position')),
        'use_item' => $event->useItem(),
        'use_block' => $event->useBlock(),
    ];
}

/** @return array<string, mixed> */
function playerInventory(Runtime $runtime, array $payload): array
{
    $slots = $payload['slots'] ?? [];
    if (!is_array($slots)) {
        $slots = [];
    }
    return [
        'synced' => $runtime->syncPlayerInventory(stringValue($payload, 'uuid'), inventorySlots($slots)),
    ];
}

/** @return array<int, Item> */
function inventorySlots(array $slots): array
{
    $items = [];
    foreach ($slots as $entry) {
        if (!is_array($entry) || !isset($entry['slot']) || !is_numeric($entry['slot'])) {
            continue;
        }
        $item = $entry['item'] ?? null;
        if (!is_array($item)) {
            continue;
        }
        $items[(int) $entry['slot']] = itemValue($item);
    }
    return $items;
}

function itemValue(array $item): Item
{
    $typeId = isset($item['type_id']) && is_scalar($item['type_id']) ? (string) $item['type_id'] : 'minecraft:air';
    $name = isset($item['name']) && is_scalar($item['name']) ? (string) $item['name'] : $typeId;
    $count = isset($item['count']) && is_numeric($item['count']) ? (int) $item['count'] : 0;
    return new Item($typeId, $name, $count);
}

function optionalItem(array $payload): ?Item
{
    return isset($payload['item']) && is_array($payload['item']) ? itemValue($payload['item']) : null;
}

function optionalBlock(array $payload): ?Block
{
    if (!isset($payload['block']) || !is_array($payload['block'])) {
        return null;
    }
    $block = $payload['block'];
    $typeId = isset($block['type_id']) && is_scalar($block['type_id']) ? (string) $block['type_id'] : 'minecraft:air';
    $name = isset($block['name']) && is_scalar($block['name']) ? (string) $block['name'] : $typeId;
    return new Block($typeId, $name);
}

/** @return array<string, mixed> */
function playerState(Runtime $runtime, array $payload): array
{
    $state = playerStatePayload($payload);
    return ['synced' => $runtime->syncPlayerState(stringValue($payload, 'uuid'), $state)];
}

/** @return array<string, mixed> */
function playerStatePayload(array $payload): array
{
    $state = [];
    if (isset($payload['position']) && is_array($payload['position'])) {
        $state['position'] = positionValue($payload, 'position');
    }
    if (isset($payload['health']) && is_numeric($payload['health'])) {
        $state['health'] = (float) $payload['health'];
    }
    if (isset($payload['max_health']) && is_numeric($payload['max_health'])) {
        $state['max_health'] = (float) $payload['max_health'];
    }
    if (isset($payload['gamemode']) && is_scalar($payload['gamemode'])) {
        $state['gamemode'] = GameMode::fromString((string) $payload['gamemode']);
    }
    if (isset($payload['xp_level']) && is_numeric($payload['xp_level'])) {
        $state['xp_level'] = (int) $payload['xp_level'];
    }
    if (isset($payload['xp_progress']) && is_numeric($payload['xp_progress'])) {
        $state['xp_progress'] = (float) $payload['xp_progress'];
    }
    return $state;
}

/** @return array<string, mixed> */
function formResponse(Runtime $runtime, array $payload): array
{
    return [
        'handled' => $runtime->formResponse(
            stringValue($payload, 'uuid'),
            intValue($payload, 'form_id'),
            $payload['data'] ?? null,
        ),
    ];
}

/** @return array<string, mixed> */
function resultOf(Closure $callback): array
{
    return $callback();
}

/** @return array<string, string> */
function playerPayload(string $uuid, string $name): array
{
    return ['uuid' => $uuid, 'name' => $name];
}

function stringValue(array $payload, string $key): string
{
    if (!isset($payload[$key]) || !is_scalar($payload[$key])) {
        throw new InvalidArgumentException("Missing string payload field: {$key}");
    }
    return (string) $payload[$key];
}

function intValue(array $payload, string $key): int
{
    if (!isset($payload[$key]) || !is_numeric($payload[$key])) {
        throw new InvalidArgumentException("Missing integer payload field: {$key}");
    }
    return (int) $payload[$key];
}

function intValueDefault(array $payload, string $key, int $default): int
{
    return isset($payload[$key]) && is_numeric($payload[$key]) ? (int) $payload[$key] : $default;
}

function floatValue(array $payload, string $key): float
{
    if (!isset($payload[$key]) || !is_numeric($payload[$key])) {
        throw new InvalidArgumentException("Missing float payload field: {$key}");
    }
    return (float) $payload[$key];
}

function vectorValue(array $payload, string $key): Vector3
{
    $value = $payload[$key] ?? null;
    if (!is_array($value)) {
        throw new InvalidArgumentException("Missing vector payload field: {$key}");
    }
    return new Vector3((float) ($value['x'] ?? 0), (float) ($value['y'] ?? 0), (float) ($value['z'] ?? 0));
}

function positionValue(array $payload, string $key): Position
{
    $value = $payload[$key] ?? null;
    if (!is_array($value)) {
        throw new InvalidArgumentException("Missing position payload field: {$key}");
    }
    $world = isset($value['world']) && is_scalar($value['world']) ? (string) $value['world'] : 'world';
    return new Position((float) ($value['x'] ?? 0), (float) ($value['y'] ?? 0), (float) ($value['z'] ?? 0), new World($world));
}

/** @return array{x: float, y: float, z: float} */
function vectorPayload(Vector3 $vector): array
{
    return ['x' => $vector->x, 'y' => $vector->y, 'z' => $vector->z];
}

/** @return array{x: float, y: float, z: float, world: string} */
function positionPayload(Position $position): array
{
    return ['x' => $position->x, 'y' => $position->y, 'z' => $position->z, 'world' => $position->getWorld()->getFolderName()];
}

/** @return array<string, mixed> */
function blockPayload(Block $block): array
{
    return ['type_id' => $block->getTypeId(), 'name' => $block->getName(), 'position' => $block->getPosition() !== null ? vectorPayload($block->getPosition()) : null];
}

function messageText(Translatable|string $message): string
{
    return $message instanceof Translatable ? $message->getText() : $message;
}

function flushRuntimeOutput(): void
{
    $output = ob_get_contents();
    if ($output !== false && $output !== '') {
        ob_clean();
        fwrite(STDERR, $output);
    }
}

function errorSeverityName(int $severity): string
{
    return match ($severity) {
        E_WARNING => 'Warning',
        E_NOTICE => 'Notice',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'User Deprecated',
        default => 'PHP Error',
    };
}

/** @return list<array{x: int, y: int, z: int, block: array<string, mixed>}> */
function transactionPayload(mixed $transaction): array
{
    if (!is_object($transaction) || !method_exists($transaction, 'getBlocks')) {
        return [];
    }
    $blocks = [];
    foreach ($transaction->getBlocks() as [$x, $y, $z, $block]) {
        $blocks[] = ['x' => $x, 'y' => $y, 'z' => $z, 'block' => $block instanceof Block ? blockPayload($block) : []];
    }
    return $blocks;
}
