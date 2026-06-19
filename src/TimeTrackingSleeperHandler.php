<?php

declare(strict_types=1);

namespace pocketmine;

final class TimeTrackingSleeperHandler
{
    /** @var array<int, \Closure> */
    private array $notifiers = [];
    private int $notificationProcessingTimeNs = 0;

    public function __construct(private mixed $timings = null) {}

    public function addNotifier(\Closure $handler): object
    {
        $id = spl_object_id($handler);
        $this->notifiers[$id] = $handler;
        return new class($this->notifiers, $id) {
            /** @param array<int, \Closure> $notifiers */
            public function __construct(private array &$notifiers, private int $id) {}
            public function createNotifier(): \Closure { return function (): void {}; }
            public function remove(): void { unset($this->notifiers[$this->id]); }
        };
    }

    public function getNotificationProcessingTime(): int
    {
        return $this->notificationProcessingTimeNs;
    }

    public function resetNotificationProcessingTime(): void
    {
        $this->notificationProcessingTimeNs = 0;
    }

    public function processNotifications(): void
    {
        $start = hrtime(true);
        foreach ($this->notifiers as $notifier) {
            $notifier();
        }
        $this->notificationProcessingTimeNs += hrtime(true) - $start;
    }
}
