<?php

declare(strict_types=1);

namespace pocketmine\timings;

abstract class Timings
{
    public const GROUP_MINECRAFT = 'Minecraft';
    public const GROUP_BREAKDOWN = 'Minecraft - Breakdown';

    private static bool $initialized = false;

    public static TimingsHandler $fullTick;
    public static TimingsHandler $serverTick;
    public static TimingsHandler $memoryManager;
    public static TimingsHandler $garbageCollector;
    public static TimingsHandler $scheduler;
    public static TimingsHandler $serverCommand;
    public static TimingsHandler $broadcastPackets;

    /** @var array<string, TimingsHandler> */
    private static array $handlers = [];

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;
        self::$fullTick = self::handler('Full Server Tick');
        self::$serverTick = self::handler('Server Tick Update Cycle');
        self::$memoryManager = self::handler('Memory Manager');
        self::$garbageCollector = self::handler('Garbage Collector');
        self::$scheduler = self::handler('Scheduler');
        self::$serverCommand = self::handler('Server Command');
        self::$broadcastPackets = self::handler('Broadcast Packets');
    }

    private static function handler(string $name, string $group = self::GROUP_MINECRAFT): TimingsHandler
    {
        return self::$handlers[$group . ':' . $name] ??= new TimingsHandler($name, $group);
    }

    public static function getScheduledTaskTimings(mixed $task, int $period): TimingsHandler
    {
        self::init();
        $name = method_exists($task, 'getTaskName') ? $task->getTaskName() : get_debug_type($task);
        return self::handler('Task: ' . $name . ($period > 0 ? '(interval:' . $period . ')' : '(Single)'));
    }

    public static function getCommandDispatchTimings(mixed $command): TimingsHandler { return self::handler('Command: ' . (string) $command); }
    public static function getEntityTimings(mixed $entity): TimingsHandler { return self::handler('Entity Tick - ' . get_debug_type($entity)); }
    public static function getTileEntityTimings(mixed $tile): TimingsHandler { return self::handler('Block Entity Tick - ' . get_debug_type($tile)); }
    public static function getEventTimings(mixed $event): TimingsHandler { return self::handler('Event: ' . get_debug_type($event)); }
    public static function getEventHandlerTimings(mixed $handler): TimingsHandler { return self::handler('Event Handler: ' . get_debug_type($handler)); }
    public static function getAsyncTaskRunTimings(mixed $task): TimingsHandler { return self::handler('Async Task Run: ' . get_debug_type($task)); }
    public static function getAsyncTaskProgressUpdateTimings(mixed $task): TimingsHandler { return self::handler('Async Task Progress: ' . get_debug_type($task)); }
    public static function getAsyncTaskCompletionTimings(mixed $task): TimingsHandler { return self::handler('Async Task Completion: ' . get_debug_type($task)); }
    public static function getAsyncTaskErrorTimings(mixed $task): TimingsHandler { return self::handler('Async Task Error: ' . get_debug_type($task)); }
    public static function getReceiveDataPacketTimings(mixed $packet): TimingsHandler { return self::handler('Receive Packet: ' . get_debug_type($packet)); }
    public static function getDecodeDataPacketTimings(mixed $packet): TimingsHandler { return self::handler('Decode Packet: ' . get_debug_type($packet)); }
    public static function getHandleDataPacketTimings(mixed $packet): TimingsHandler { return self::handler('Handle Packet: ' . get_debug_type($packet)); }
    public static function getEncodeDataPacketTimings(mixed $packet): TimingsHandler { return self::handler('Encode Packet: ' . get_debug_type($packet)); }
    public static function getSendDataPacketTimings(mixed $packet): TimingsHandler { return self::handler('Send Packet: ' . get_debug_type($packet)); }
}
