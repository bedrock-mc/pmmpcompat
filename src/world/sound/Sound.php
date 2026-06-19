<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

interface Sound
{
    public function encode(mixed ...$args): mixed;
}
