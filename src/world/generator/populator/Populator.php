<?php

declare(strict_types=1);

namespace pocketmine\world\generator\populator;

interface Populator
{
    public function populate(mixed $world = null, int $chunkX = 0, int $chunkZ = 0, mixed $random = null): void;
}
