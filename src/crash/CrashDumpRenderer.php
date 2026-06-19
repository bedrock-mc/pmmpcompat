<?php

declare(strict_types=1);

namespace pocketmine\crash;

final class CrashDumpRenderer
{
    /** @param resource $fp */
    public function __construct(private $fp, private CrashDumpData $data) {}

    public function addLine(string $line = ''): void
    {
        fwrite($this->fp, $line . PHP_EOL);
    }

    public function renderHumanReadable(): void
    {
        $this->addLine($this->data->general->name . ' Crash Dump ' . date('D M j H:i:s T Y', (int) $this->data->time));
        $this->addLine($this->data->general->name . ' version: ' . $this->data->general->base_version);
        $this->addLine('Git commit: ' . $this->data->general->git);
        $this->addLine('PHP version: ' . $this->data->general->php);
        $this->addLine('OS: ' . $this->data->general->php_os . ', ' . $this->data->general->os);
        $this->addLine('Thread: ' . $this->data->thread);
        $this->addLine('Error: ' . ($this->data->error['message'] ?? 'unknown'));
        $this->addLine('File: ' . ($this->data->error['file'] ?? 'unknown'));
        $this->addLine('Line: ' . ($this->data->error['line'] ?? 0));
        $this->addLine('Type: ' . ($this->data->error['type'] ?? 'unknown'));
        foreach ($this->data->trace as $line) {
            $this->addLine($line);
        }
    }
}
