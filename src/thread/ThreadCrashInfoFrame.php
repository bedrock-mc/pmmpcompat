<?php

declare(strict_types=1);

namespace pocketmine\thread;

class ThreadCrashInfoFrame
{
    public function __construct(
        private string $printableFrame,
        private ?string $file,
        private int $line,
    ) {
    }

    public function getPrintableFrame(): string
    {
        return $this->printableFrame;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
