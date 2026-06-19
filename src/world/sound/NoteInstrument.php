<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

use pocketmine\utils\LegacyEnumShimTrait;

enum NoteInstrument
{
    use LegacyEnumShimTrait;

    case PIANO;
    case BASS_DRUM;
    case SNARE;
    case CLICKS_AND_STICKS;
    case DOUBLE_BASS;
    case BELL;
    case FLUTE;
    case CHIME;
    case GUITAR;
    case XYLOPHONE;
    case IRON_XYLOPHONE;
    case COW_BELL;
    case DIDGERIDOO;
    case BIT;
    case BANJO;
    case PLING;
}
