<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class StringToEffectParser
{
    public function parse(string $input): ?Effect
    {
        $key = strtolower(str_replace([' ', 'minecraft:'], ['_', ''], $input));
        return VanillaEffects::getAll()[$key] ?? null;
    }
}
