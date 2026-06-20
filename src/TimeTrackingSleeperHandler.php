<?php

declare(strict_types=1);

namespace pocketmine;

final class TimeTrackingSleeperHandler extends \pocketmine\snooze\SleeperHandler
{
    private int $notificationProcessingTimeNs = 0;

    public function __construct(private mixed $timings = null)
    {
        parent::__construct();
    }

    public function addNotifier(\Closure|\pocketmine\snooze\SleeperNotifier $handler, ?\Closure $legacyHandler = null): \pocketmine\snooze\SleeperHandlerEntry
    {
        return parent::addNotifier($handler, $legacyHandler);
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
        parent::processNotifications();
        $this->notificationProcessingTimeNs += hrtime(true) - $start;
    }
}
