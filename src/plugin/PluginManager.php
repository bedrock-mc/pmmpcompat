<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerListManager;
use pocketmine\event\Listener;
use pocketmine\event\ListenerMethodTags;
use pocketmine\event\RegisteredListener;
use pocketmine\Server;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\Utils;

class PluginManager
{
    /** @var array<class-string<Event>, list<callable(Event): void>> */
    private array $handlers = [];
    /** @var array<string, PluginBase> */
    private array $plugins = [];

    public function __construct(private Server $server)
    {
        Event::setEventDispatcher(null);
    }

    /** @return array<class-string<Event>, list<callable(Event): void>> */
    public function handlers(): array
    {
        return $this->handlers;
    }

    public function registerEvents(Listener $listener, PluginBase $plugin): void
    {
        if (!$plugin->isEnabled()) {
            throw new \LogicException('Plugin attempted to register events while disabled.');
        }
        $ref = new \ReflectionClass($listener);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic() || $method->getNumberOfParameters() !== 1) {
                continue;
            }
            $type = $method->getParameters()[0]->getType();
            if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }
            $class = $type->getName();
            if (!is_subclass_of($class, Event::class)) {
                continue;
            }
            $tags = Utils::parseDocComment((string) $method->getDocComment());
            if (isset($tags[ListenerMethodTags::NOT_HANDLER])) {
                continue;
            }
            $priority = isset($tags[ListenerMethodTags::PRIORITY]) ? EventPriority::fromString($tags[ListenerMethodTags::PRIORITY]) : EventPriority::NORMAL;
            $handleCancelled = isset($tags[ListenerMethodTags::HANDLE_CANCELLED]) && !in_array(strtolower($tags[ListenerMethodTags::HANDLE_CANCELLED]), ['false', 'no', '0'], true);
            $name = $method->getName();
            $this->registerEvent($class, fn(Event $event): mixed => $listener->{$name}($event), $priority, $plugin, $handleCancelled);
        }
    }

    public function registerEvent(string $event, \Closure $handler, int $priority, Plugin $plugin, bool $handleCancelled = false): RegisteredListener
    {
        if (!is_subclass_of($event, Event::class)) {
            throw new \InvalidArgumentException("Event class must extend " . Event::class);
        }
        if (!$plugin->isEnabled()) {
            throw new \LogicException('Plugin attempted to register events while disabled.');
        }
        $registered = new RegisteredListener($handler, $priority, $plugin, $handleCancelled, new TimingsHandler($event . ':' . Utils::getNiceClosureName($handler), $plugin->getDescription()->getFullName()));
        HandlerListManager::global()->getListFor($event)->register($registered);
        $this->handlers[$event][] = $handler;
        return $registered;
    }

    public function registerInterface(object $loader): void {}

    public function registerPlugin(PluginBase $plugin): void
    {
        $this->plugins[strtolower($plugin->getName())] = $plugin;
    }

    public function getPlugin(string $name): ?PluginBase
    {
        return $this->plugins[strtolower($name)] ?? null;
    }

    public function isPluginEnabled(PluginBase|string $plugin): bool
    {
        if (is_string($plugin)) {
            $plugin = $this->getPlugin($plugin);
        }
        return $plugin instanceof PluginBase && $plugin->isEnabled();
    }

    public function enablePlugin(PluginBase $plugin): void
    {
        $plugin->__pmmpCallEnable();
    }

    public function disablePlugin(PluginBase $plugin): void
    {
        $plugin->__pmmpCallDisable();
    }

    public function disablePlugins(): void
    {
        foreach (array_reverse($this->getPlugins()) as $plugin) {
            $this->disablePlugin($plugin);
        }
    }

    public function clearPlugins(): void
    {
        $this->disablePlugins();
        $this->plugins = [];
        $this->handlers = [];
        HandlerListManager::global()->unregisterAll();
    }

    /** @return PluginBase[] */
    public function loadPlugins(string $path, int &$loadErrorCount = 0): array
    {
        try {
            return (new PluginLoader($this->server))->loadDirectory($path);
        } catch (\Throwable) {
            $loadErrorCount++;
            return [];
        }
    }

    /** @return PluginBase[] */
    public function getPlugins(): array
    {
        return array_values($this->plugins);
    }

    public function callEvent(Event $event): void
    {
        foreach (HandlerListManager::global()->getHandlersFor($event::class) as $registration) {
            $registration->callEvent($event);
        }
    }

    public function tickSchedulers(int $currentTick): void
    {
        $this->server->tickSchedulers($currentTick);
        foreach ($this->plugins as $plugin) {
            $plugin->__pmmpTickScheduler($currentTick);
        }
    }
}
