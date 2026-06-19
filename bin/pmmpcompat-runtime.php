#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\compat\HostActionQueue;
use pocketmine\compat\Runtime;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;

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
        fwrite(STDOUT, json_encode(['id' => $id, 'ok' => true, 'result' => $result, 'actions' => $queue->drain()]) . "\n");
    } catch (Throwable $e) {
        fwrite(STDOUT, json_encode(['id' => $id, 'ok' => false, 'error' => $e->getMessage(), 'actions' => $queue->drain()]) . "\n");
    }
    fflush(STDOUT);
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
        'disable' => resultOf(static function () use ($runtime): array {
            $runtime->disable();
            return ['disabled' => true];
        }),
        'player_join' => playerJoin($runtime, $queue, $payload),
        'player_quit' => playerQuit($runtime, $payload),
        'chat' => chat($runtime, $payload),
        'command' => command($runtime, $payload),
        'player_move' => playerMove($runtime, $payload),
        'block_break' => blockBreak($runtime, $payload),
        'block_place' => blockPlace($runtime, $payload),
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
function playerJoin(Runtime $runtime, HostActionQueue $queue, array $payload): array
{
    $uuid = stringValue($payload, 'uuid');
    $event = $runtime->playerJoin($uuid, stringValue($payload, 'name'), $queue->forPlayer($uuid));
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
    return ['cancelled' => $event->isCancelled(), 'message' => $event->getMessage()];
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
    $event = $runtime->playerMove(stringValue($payload, 'uuid'), stringValue($payload, 'name'), vectorValue($payload, 'to'));
    return ['cancelled' => $event->isCancelled(), 'from' => vectorPayload($event->getFrom()), 'to' => vectorPayload($event->getTo())];
}

/** @return array<string, mixed> */
function blockBreak(Runtime $runtime, array $payload): array
{
    $event = $runtime->blockBreak(stringValue($payload, 'uuid'), stringValue($payload, 'name'), vectorValue($payload, 'position'));
    return ['cancelled' => $event->isCancelled(), 'position' => vectorPayload($event->getBlockPosition())];
}

/** @return array<string, mixed> */
function blockPlace(Runtime $runtime, array $payload): array
{
    $event = $runtime->blockPlace(stringValue($payload, 'uuid'), stringValue($payload, 'name'), vectorValue($payload, 'position'));
    return ['cancelled' => $event->isCancelled(), 'position' => vectorPayload($event->getBlockPosition())];
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

/** @return array<string, mixed> */
function playerState(Runtime $runtime, array $payload): array
{
    $state = [];
    if (isset($payload['position']) && is_array($payload['position'])) {
        $position = $payload['position'];
        $world = isset($position['world']) && is_scalar($position['world']) ? (string) $position['world'] : 'world';
        $state['position'] = new Position((float) ($position['x'] ?? 0), (float) ($position['y'] ?? 0), (float) ($position['z'] ?? 0), new World($world));
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
    return ['synced' => $runtime->syncPlayerState(stringValue($payload, 'uuid'), $state)];
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

function vectorValue(array $payload, string $key): Vector3
{
    $value = $payload[$key] ?? null;
    if (!is_array($value)) {
        throw new InvalidArgumentException("Missing vector payload field: {$key}");
    }
    return new Vector3((float) ($value['x'] ?? 0), (float) ($value['y'] ?? 0), (float) ($value['z'] ?? 0));
}

/** @return array{x: float, y: float, z: float} */
function vectorPayload(Vector3 $vector): array
{
    return ['x' => $vector->x, 'y' => $vector->y, 'z' => $vector->z];
}
