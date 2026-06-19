<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\cache;

class StaticPacketCache
{
    public function __construct(
        private mixed $availableActorIdentifiers = null,
        private mixed $biomeDefs = null
    ) {}

    public function getAvailableActorIdentifiers(): mixed { return $this->availableActorIdentifiers; }
    public function getBiomeDefs(): mixed { return $this->biomeDefs; }
}
