<?php

declare(strict_types=1);

namespace pocketmine\timings;

class TimingsHandler
{
    private static bool $enabled = false;
    private static float $startTime;
    /** @var list<callable> */
    private static array $collectCallbacks = [];
    /** @var list<callable> */
    private static array $reloadCallbacks = [];
    /** @var list<callable> */
    private static array $toggleCallbacks = [];
    private float $totalTime = 0.0;
    private ?float $startedAt = null;

    public function __construct(private string $name = 'unknown', private string $group = 'pmmpcompat')
    {
        self::$startTime ??= microtime(true);
    }

    public function getGroup(): string { return $this->group; }
    public function getName(): string { return $this->name; }
    public static function getCollectCallbacks(): array { return self::$collectCallbacks; }
    public static function getReloadCallbacks(): array { return self::$reloadCallbacks; }
    public static function getStartTime(): float { return self::$startTime ??= microtime(true); }
    public static function getToggleCallbacks(): array { return self::$toggleCallbacks; }
    public static function isEnabled(): bool { return self::$enabled; }
    public static function printCurrentThreadRecords(mixed ...$args): void {}
    public static function printTimings(mixed ...$args): void {}
    public static function reload(): void { foreach (self::$reloadCallbacks as $callback) { $callback(); } }
    public static function requestPrintTimings(mixed ...$args): void {}
    public function reset(): void { $this->totalTime = 0.0; $this->startedAt = null; }
    public static function setEnabled(bool $enabled = true): void { self::$enabled = $enabled; foreach (self::$toggleCallbacks as $callback) { $callback($enabled); } }
    public function startTiming(): void { if (self::$enabled) { $this->startedAt = microtime(true); } }
    public function stopTiming(): void { if ($this->startedAt !== null) { $this->totalTime += microtime(true) - $this->startedAt; $this->startedAt = null; } }
    public static function tick(mixed ...$args): void {}
    public function time(\Closure $closure): mixed { $this->startTiming(); try { return $closure(); } finally { $this->stopTiming(); } }
}
