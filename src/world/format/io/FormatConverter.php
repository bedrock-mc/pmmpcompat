<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class FormatConverter
{
    private string $backupPath;

    public function __construct(private WorldProvider $oldProvider, private WritableWorldProviderManagerEntry $newProvider, string $backupPath, private mixed $logger = null, private int $chunksPerProgressUpdate = 256)
    {
        $this->backupPath = rtrim($backupPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($oldProvider->getPath());
    }
    public function execute(): void {}
    public function getBackupPath(): string { return $this->backupPath; }
}
