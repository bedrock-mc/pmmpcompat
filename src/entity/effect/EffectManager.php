<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class EffectManager
{
    public function __construct(private EffectCollection $effects = new EffectCollection()) {}

    public function add(EffectInstance $effect): bool { return $this->effects->add($effect); }
    public function remove(mixed $effect): bool { return $this->effects->remove($effect); }
    public function tick(int $tickDiff = 1): void
    {
        foreach ($this->effects->all() as $effect) {
            $effect->decreaseDuration($tickDiff);
            if ($effect->hasExpired()) {
                $this->effects->remove($effect);
            }
        }
    }
}
