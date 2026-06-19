<?php

declare(strict_types=1);

namespace pocketmine\utils;

class MainLogger
{
    /** @var list<array{level: string, message: string}> */
    private array $records = [];
    private string $format = '[%s][%s] %s';
    private bool $logDebug = true;

    public function __construct(private string $prefix = 'pmmpcompat') {}

    public function __destruct() {}

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function setLogDebug(bool $logDebug): void
    {
        $this->logDebug = $logDebug;
    }

    public function emergency(mixed $message): void { $this->log('emergency', (string) $message); }
    public function alert(mixed $message): void { $this->log('alert', (string) $message); }
    public function critical(mixed $message): void { $this->log('critical', (string) $message); }
    public function info(mixed $message): void { $this->log('info', (string) $message); }
    public function notice(mixed $message): void { $this->log('notice', (string) $message); }
    public function warning(mixed $message): void { $this->log('warning', (string) $message); }
    public function error(mixed $message): void { $this->log('error', (string) $message); }
    public function debug(mixed $message, bool $force = false): void
    {
        if ($this->logDebug || $force) {
            $this->log('debug', (string) $message);
        }
    }

    public function log(mixed $level, mixed $message): void
    {
        $level = (string) $level;
        $message = (string) $message;
        $this->records[] = ['level' => $level, 'message' => $message];
        fwrite(STDERR, '[' . $this->prefix . '][' . $level . '] ' . $message . PHP_EOL);
    }

    public function logException(\Throwable $e, ?array $trace = null): void
    {
        $this->critical($e::class . ': ' . $e->getMessage());
        foreach (($trace ?? $e->getTrace()) as $frame) {
            $this->critical((string) ($frame['file'] ?? '<unknown>') . ':' . (string) ($frame['line'] ?? 0));
        }
    }

    public function buffer(\Closure $closure): void
    {
        $closure();
    }

    public function shutdownLogWriterThread(): void {}
    public function syncFlushBuffer(): void {}

    /** @return list<array{level: string, message: string}> */
    public function records(): array
    {
        return $this->records;
    }
}
