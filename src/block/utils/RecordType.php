<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\LegacyEnumShimTrait;

enum RecordType
{
    use LegacyEnumShimTrait;

    case DISK_13;
    case DISK_5;
    case DISK_CAT;
    case DISK_BLOCKS;
    case DISK_CHIRP;
    case DISK_CREATOR;
    case DISK_CREATOR_MUSIC_BOX;
    case DISK_FAR;
    case DISK_MALL;
    case DISK_MELLOHI;
    case DISK_OTHERSIDE;
    case DISK_PIGSTEP;
    case DISK_PRECIPICE;
    case DISK_RELIC;
    case DISK_STAL;
    case DISK_STRAD;
    case DISK_WARD;
    case DISK_11;
    case DISK_WAIT;

    public function getSoundId(mixed ...$args): mixed { return array_search($this, self::cases(), true); }

    public function getSoundName(mixed ...$args): mixed { return str_replace('_', ' ', strtolower($this->name)); }

    public function getTranslatableName(mixed ...$args): mixed { return $this->getSoundName(); }
}
