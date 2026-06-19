<?php

declare(strict_types=1);

namespace pocketmine\timings;

final class TimingsRecord
{
    /** @var array<int, self> */
    private static array $records = [];
    private static ?self $currentRecord = null;
    private int $count = 0;
    private int $curCount = 0;
    private int $start = 0;
    private int $totalTime = 0;
    private int $curTickTotal = 0;
    private int $violations = 0;
    private int $ticksActive = 0;
    private int $peakTime = 0;

    public function __construct(private TimingsHandler $handler, private ?TimingsRecord $parentRecord = null)
    {
        self::$records[spl_object_id($this)] = $this;
    }

    public static function reset(): void { self::$records = []; self::$currentRecord = null; }
    /** @return array<int, self> */
    public static function getAll(): array { return self::$records; }
    public static function tick(bool $measure = true): void
    {
        foreach (self::$records as $record) {
            if ($record->curCount > 0) {
                $record->ticksActive++;
            }
            $record->curTickTotal = 0;
            $record->curCount = 0;
        }
    }
    public function getId(): int { return spl_object_id($this); }
    public function getParentId(): ?int { return $this->parentRecord?->getId(); }
    public function getTimerId(): int { return spl_object_id($this->handler); }
    public function getName(): string { return $this->handler->getName(); }
    public function getGroup(): string { return $this->handler->getGroup(); }
    public function getCount(): int { return $this->count; }
    public function getCurCount(): int { return $this->curCount; }
    public function getStart(): float { return $this->start; }
    public function getTotalTime(): float { return $this->totalTime; }
    public function getCurTickTotal(): float { return $this->curTickTotal; }
    public function getViolations(): int { return $this->violations; }
    public function getTicksActive(): int { return $this->ticksActive; }
    public function getPeakTime(): int { return $this->peakTime; }
    public function startTiming(int $now): void { $this->start = $now; self::$currentRecord = $this; }
    public function stopTiming(int $now): void
    {
        if ($this->start === 0) {
            return;
        }
        self::$currentRecord = $this->parentRecord;
        $diff = max(0, $now - $this->start);
        $this->totalTime += $diff;
        $this->curTickTotal += $diff;
        $this->curCount++;
        $this->count++;
        $this->peakTime = max($this->peakTime, $diff);
        $this->start = 0;
    }
    public static function getCurrentRecord(): ?self { return self::$currentRecord; }
}
