<?php

declare(strict_types=1);

namespace pocketmine\utils;

class MainLoggerThread
{
    /** @var list<string> */
    private array $buffer = [];
    private bool $shutdown = false;

    public function __construct(
        private string $logFile,
        private ?string $archiveDir = null,
        private int $maxFileSize = 33554432,
    ) {
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        touch($this->logFile);
        if ($archiveDir !== null && !is_dir($archiveDir)) {
            mkdir($archiveDir, 0777, true);
        }
    }

    public function write(string $line): void
    {
        $this->buffer[] = $line;
    }

    public function syncFlushBuffer(): void
    {
        if ($this->buffer === []) {
            return;
        }
        file_put_contents($this->logFile, implode('', $this->buffer), FILE_APPEND);
        $this->buffer = [];
    }

    public function shutdown(): void
    {
        $this->shutdown = true;
        $this->syncFlushBuffer();
    }

    public function run(): void
    {
        if (!$this->shutdown) {
            $this->syncFlushBuffer();
        }
    }
}
