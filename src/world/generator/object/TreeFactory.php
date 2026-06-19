<?php

declare(strict_types=1);

namespace pocketmine\world\generator\object;

use pocketmine\utils\Random;

final class TreeFactory
{
    public static function get(Random $random, ?TreeType $type = null): ?Tree
    {
        return match ($type) {
            null, TreeType::OAK => new OakTree(),
            TreeType::SPRUCE => new SpruceTree(),
            TreeType::JUNGLE => new JungleTree(),
            TreeType::ACACIA => new AcaciaTree(),
            TreeType::BIRCH => new BirchTree($random->nextBoundedInt(39) === 0),
            default => null,
        };
    }
}
