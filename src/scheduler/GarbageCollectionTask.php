<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class GarbageCollectionTask extends AsyncTask
{
    public function onRun(): void
    {
        gc_enable();
        gc_collect_cycles();
        if (function_exists('gc_mem_caches')) {
            gc_mem_caches();
        }
    }
}
