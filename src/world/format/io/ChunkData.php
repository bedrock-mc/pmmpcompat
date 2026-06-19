<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class ChunkData
{
    public function __construct(private array $subChunks = [], private bool $populated = false, private array $entityNBT = [], private array $tileNBT = []) {}
    public function getEntityNBT(): array { return $this->entityNBT; }
    public function getSubChunks(): array { return $this->subChunks; }
    public function getTileNBT(): array { return $this->tileNBT; }
    public function isPopulated(): bool { return $this->populated; }
}
