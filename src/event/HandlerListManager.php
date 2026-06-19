<?php

declare(strict_types=1);

namespace pocketmine\event;

use pocketmine\plugin\Plugin;

class HandlerListManager
{
    private static ?self $globalInstance = null;

    /** @var array<class-string<Event>, HandlerList> */
    private array $allLists = [];
    /** @var array<class-string<Event>, RegisteredListenerCache> */
    private array $handlerCaches = [];

    public static function global(): self
    {
        return self::$globalInstance ??= new self();
    }

    public static function self(): self
    {
        return self::global();
    }

    public function unregisterAll(RegisteredListener|Plugin|Listener|null $object = null): void
    {
        foreach ($this->allLists as $list) {
            if ($object instanceof RegisteredListener || $object instanceof Plugin || $object instanceof Listener) {
                $list->unregister($object);
            } else {
                $list->clear();
            }
        }
    }

    public function getListFor(string $event): HandlerList
    {
        if (!is_a($event, Event::class, true)) {
            throw new \InvalidArgumentException('Event class must extend ' . Event::class);
        }
        if (isset($this->allLists[$event])) {
            return $this->allLists[$event];
        }

        $parentList = null;
        for ($parent = get_parent_class($event); $parent !== false; $parent = get_parent_class($parent)) {
            if (is_a($parent, Event::class, true)) {
                $parentList = $this->getListFor($parent);
                break;
            }
        }

        $cache = new RegisteredListenerCache();
        $this->handlerCaches[$event] = $cache;
        return $this->allLists[$event] = new HandlerList($event, $parentList, $cache);
    }

    /** @return list<RegisteredListener> */
    public function getHandlersFor(string $event): array
    {
        return ($this->handlerCaches[$event] ?? null)?->list ?? $this->getListFor($event)->getListenerList();
    }

    /** @return array<class-string<Event>, HandlerList> */
    public function getAll(): array
    {
        return $this->allLists;
    }
}
