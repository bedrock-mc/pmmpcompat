<?php

declare(strict_types=1);

namespace pocketmine\event;

use pocketmine\plugin\Plugin;

class HandlerList
{
    /** @var array<int, array<int, RegisteredListener>> */
    private array $handlerSlots = [];
    /** @var array<int, RegisteredListenerCache> */
    private array $affectedHandlerCaches = [];

    public function __construct(
        private string $class,
        private ?HandlerList $parentList,
        private RegisteredListenerCache $handlerCache = new RegisteredListenerCache()
    ) {
        for ($list = $this; $list !== null; $list = $list->parentList) {
            $list->affectedHandlerCaches[spl_object_id($this->handlerCache)] = $this->handlerCache;
        }
    }

    public function register(RegisteredListener $listener): void
    {
        $id = spl_object_id($listener);
        if (isset($this->handlerSlots[$listener->getPriority()][$id])) {
            throw new \InvalidArgumentException('This listener is already registered to priority ' . $listener->getPriority() . ' of event ' . $this->class);
        }
        $this->handlerSlots[$listener->getPriority()][$id] = $listener;
        $this->invalidateAffectedCaches();
    }

    /** @param list<RegisteredListener> $listeners */
    public function registerAll(array $listeners): void
    {
        foreach ($listeners as $listener) {
            $this->register($listener);
        }
    }

    public function unregister(RegisteredListener|Plugin|Listener $object): void
    {
        if ($object instanceof Plugin || $object instanceof Listener) {
            foreach ($this->handlerSlots as $priority => $listeners) {
                foreach ($listeners as $hash => $listener) {
                    $closureThis = (new \ReflectionFunction($listener->getHandler()))->getClosureThis();
                    if (($object instanceof Plugin && $listener->getPlugin() === $object) || ($object instanceof Listener && $closureThis === $object)) {
                        unset($this->handlerSlots[$priority][$hash]);
                    }
                }
            }
        } else {
            unset($this->handlerSlots[$object->getPriority()][spl_object_id($object)]);
        }
        $this->invalidateAffectedCaches();
    }

    public function clear(): void
    {
        $this->handlerSlots = [];
        $this->invalidateAffectedCaches();
    }

    /** @return array<int, RegisteredListener> */
    public function getListenersByPriority(int $priority): array
    {
        return $this->handlerSlots[$priority] ?? [];
    }

    public function getParent(): ?HandlerList
    {
        return $this->parentList;
    }

    /** @return list<RegisteredListener> */
    public function getListenerList(): array
    {
        if ($this->handlerCache->list !== null) {
            return $this->handlerCache->list;
        }

        $listenersByPriority = [];
        for ($currentList = $this; $currentList !== null; $currentList = $currentList->parentList) {
            foreach ($currentList->handlerSlots as $priority => $listeners) {
                $listenersByPriority[$priority] = array_merge($listenersByPriority[$priority] ?? [], $listeners);
            }
        }

        if ($listenersByPriority === []) {
            return $this->handlerCache->list = [];
        }

        krsort($listenersByPriority, SORT_NUMERIC);
        return $this->handlerCache->list = array_values(array_merge(...array_values($listenersByPriority)));
    }

    private function invalidateAffectedCaches(): void
    {
        foreach ($this->affectedHandlerCaches as $cache) {
            $cache->list = null;
        }
    }
}
