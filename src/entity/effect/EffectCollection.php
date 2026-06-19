<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

use pocketmine\color\Color;

class EffectCollection
{
    /** @var array<string, EffectInstance> */
    private array $effects = [];
    /** @var callable[] */
    private array $addHooks = [];
    /** @var callable[] */
    private array $removeHooks = [];
    /** @var null|callable(EffectInstance): bool */
    private $bubbleFilter = null;

    public function __construct(callable ...$hooks) { $this->addHooks = $hooks; }

    public function canAdd(EffectInstance $effect): bool { return true; }
    public function add(EffectInstance $effect): bool
    {
        if (!$this->canAdd($effect)) {
            return false;
        }
        $key = $this->key($effect->getType());
        $this->effects[$key] = $effect;
        foreach ($this->addHooks as $hook) {
            $hook($effect);
        }
        return true;
    }
    public function remove(mixed $effect): bool
    {
        $key = $this->key($effect);
        if (!isset($this->effects[$key])) {
            return false;
        }
        $removed = $this->effects[$key];
        unset($this->effects[$key]);
        foreach ($this->removeHooks as $hook) {
            $hook($removed);
        }
        return true;
    }
    public function clear(): void { foreach (array_keys($this->effects) as $key) { $this->remove($key); } }
    public function get(mixed $effect): ?EffectInstance { return $this->effects[$this->key($effect)] ?? null; }
    public function has(mixed $effect): bool { return $this->get($effect) !== null; }
    /** @return array<string, EffectInstance> */
    public function all(): array { return $this->effects; }
    public function hasOnlyAmbientEffects(): bool
    {
        return $this->effects !== [] && array_reduce($this->effects, static fn(bool $carry, EffectInstance $effect): bool => $carry && $effect->isAmbient(), true);
    }
    public function getBubbleColor(): ?Color
    {
        foreach ($this->effects as $effect) {
            if ($this->bubbleFilter !== null && !($this->bubbleFilter)($effect)) {
                continue;
            }
            if (($color = $effect->getColor()) !== null) {
                return $color;
            }
            $type = $effect->getType();
            if ($type instanceof Effect && $type->hasBubbles()) {
                return $type->getColor();
            }
        }
        return null;
    }
    public function setEffectFilterForBubbles(?callable $filter): void { $this->bubbleFilter = $filter; }
    public function getEffectAddHooks(): array { return $this->addHooks; }
    public function getEffectRemoveHooks(): array { return $this->removeHooks; }

    private function key(mixed $effect): string
    {
        if ($effect instanceof EffectInstance) {
            return $this->key($effect->getType());
        }
        if ($effect instanceof Effect) {
            return $effect->getName();
        }
        return (string) $effect;
    }
}
