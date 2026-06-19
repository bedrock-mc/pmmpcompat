<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class DumpWorkerMemoryTask extends AsyncTask
{
    public function __construct(private string $fileName = 'worker-memory')
    {
    }

    public function onRun(): void
    {
        $this->setResult(['file' => $this->fileName, 'memory' => memory_get_usage(true)]);
    }
}
