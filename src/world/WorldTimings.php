<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\timings\TimingsHandler;

class WorldTimings
{
    public TimingsHandler $doTick;
    public TimingsHandler $tickEntities;
    public TimingsHandler $syncChunkSend;
    public TimingsHandler $chunkLoad;
    public TimingsHandler $chunkUnload;

    public function __construct(World|string $world)
    {
        $name = $world instanceof World ? $world->getFolderName() : $world;
        $this->doTick = new TimingsHandler("World $name - doTick");
        $this->tickEntities = new TimingsHandler("World $name - tickEntities");
        $this->syncChunkSend = new TimingsHandler("World $name - syncChunkSend");
        $this->chunkLoad = new TimingsHandler("World $name - chunkLoad");
        $this->chunkUnload = new TimingsHandler("World $name - chunkUnload");
    }
}
