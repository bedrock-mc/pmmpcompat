<?php

declare(strict_types=1);

namespace pocketmine\event;

class Event
{
    /** @var null|\Closure(Event): void */
    private static ?\Closure $dispatcher = null;
    private static int $eventCallDepth = 0;
    private const MAX_EVENT_CALL_DEPTH = 50;

    public static function setEventDispatcher(?\Closure $dispatcher): void
    {
        self::$dispatcher = $dispatcher;
    }

    public function call(): void
    {
        if (self::$dispatcher !== null) {
            (self::$dispatcher)($this);
            return;
        }
        if (self::$eventCallDepth >= self::MAX_EVENT_CALL_DEPTH) {
            throw new \RuntimeException('Recursive event call detected');
        }
        ++self::$eventCallDepth;
        try {
            foreach (HandlerListManager::global()->getHandlersFor(static::class) as $registration) {
                $registration->callEvent($this);
            }
        } finally {
            --self::$eventCallDepth;
        }
    }

    public function getEventName(): string
    {
        $parts = explode('\\', static::class);
        return end($parts) ?: static::class;
    }

    public static function hasHandlers(): bool
    {
        return self::$dispatcher !== null || count(HandlerListManager::global()->getHandlersFor(static::class)) > 0;
    }
}
