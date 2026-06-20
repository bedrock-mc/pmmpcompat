<?php

declare(strict_types=1);

namespace pocketmine\snooze;

class SleeperHandler
{
    /** @var array<int, array<int, \Closure>> */
    private static array $registry = [];

    /** @var array<int, SleeperNotifierState> */
    private static array $states = [];

    /** @var array<int, \Closure> */
    private array $notifiers = [];
    private int $nextId = 1;
    private int $handlerId;
    private SleeperNotifierState $state;

    public function __construct()
    {
        $this->handlerId = spl_object_id($this);
        $this->state = new SleeperNotifierState();
        self::$registry[$this->handlerId] = [];
        self::$states[$this->handlerId] = $this->state;
    }

    public function addNotifier(\Closure|SleeperNotifier $handler, ?\Closure $legacyHandler = null): SleeperHandlerEntry
    {
        $externalNotifier = null;
        if ($handler instanceof SleeperNotifier) {
            $externalNotifier = $handler;
            $handler = $legacyHandler ?? static function (): void {};
        }
        $id = $this->nextId++;
        $this->notifiers[$id] = $handler;
        self::$registry[$this->handlerId][$id] = $handler;
        if ($externalNotifier !== null) {
            $externalNotifier->bind($this->state, $id);
        }
        return new SleeperHandlerEntry($this->handlerId, $this->state, $id);
    }

    public function removeNotifier(int $notifierId): void
    {
        unset($this->notifiers[$notifierId], self::$registry[$this->handlerId][$notifierId], $this->state->pending[$notifierId]);
    }

    public function notify(int $notifierId): void
    {
        if (isset($this->notifiers[$notifierId])) {
            $this->state->pending[$notifierId] = true;
        }
    }

    public function processNotifications(): void
    {
        $pending = array_keys($this->state->pending->toArray());
        foreach ($pending as $id) {
            unset($this->state->pending[$id]);
        }
        foreach ($pending as $id) {
            ($this->notifiers[$id] ?? static function (): void {})();
        }
    }

    public static function removeRegisteredNotifier(int $handlerId, int $notifierId): void
    {
        unset(self::$registry[$handlerId][$notifierId]);
        if (isset(self::$states[$handlerId])) {
            unset(self::$states[$handlerId]->pending[$notifierId]);
        }
    }
}
