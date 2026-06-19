<?php

declare(strict_types=1);

namespace pocketmine\thread;

class ThreadCrashInfo
{
    /** @param ThreadCrashInfoFrame[] $trace */
    public function __construct(
        private string $type,
        private string $message,
        private string $file,
        private int $line,
        private array $trace,
        private string $threadName,
    ) {
    }

    public static function fromThrowable(\Throwable $e, string $threadName): self
    {
        $trace = [];
        foreach ($e->getTrace() as $index => $frame) {
            $file = isset($frame['file']) ? (string) $frame['file'] : null;
            $line = isset($frame['line']) ? (int) $frame['line'] : 0;
            $trace[] = new ThreadCrashInfoFrame('#' . $index . ' ' . ($file ?? '<internal>') . '(' . $line . ')', $file, $line);
        }
        return new self($e::class, $e->getMessage(), $e->getFile(), $e->getLine(), $trace, $threadName);
    }

    public static function fromLastErrorInfo(array $info, string $threadName): self
    {
        return new self(
            'PHP error ' . (string) ($info['type'] ?? 0),
            (string) ($info['message'] ?? ''),
            (string) ($info['file'] ?? ''),
            (int) ($info['line'] ?? 0),
            [],
            $threadName,
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    public function getThreadName(): string
    {
        return $this->threadName;
    }

    public function makePrettyMessage(): string
    {
        return $this->type . ': "' . $this->message . '" in "' . $this->file . '" on line ' . $this->line;
    }
}
