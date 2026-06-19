<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\world\sound\BlastFurnaceSound;
use pocketmine\world\sound\CampfireSound;
use pocketmine\world\sound\FurnaceSound;
use pocketmine\world\sound\SmokerSound;
use pocketmine\world\sound\Sound;

enum FurnaceType
{
    case FURNACE;
    case BLAST_FURNACE;
    case SMOKER;
    case CAMPFIRE;
    case SOUL_CAMPFIRE;

    public function getCookDurationTicks(): int
    {
        return match ($this) {
            self::FURNACE => 200,
            self::BLAST_FURNACE, self::SMOKER => 100,
            self::CAMPFIRE, self::SOUL_CAMPFIRE => 600,
        };
    }

    public function getCookSound(): Sound
    {
        return match ($this) {
            self::FURNACE => new FurnaceSound(),
            self::BLAST_FURNACE => new BlastFurnaceSound(),
            self::SMOKER => new SmokerSound(),
            self::CAMPFIRE, self::SOUL_CAMPFIRE => new CampfireSound(),
        };
    }
}
