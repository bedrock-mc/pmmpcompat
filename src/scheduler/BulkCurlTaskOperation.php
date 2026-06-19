<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class BulkCurlTaskOperation
{
    public function __construct(
        private string $page,
        private float $timeout = 10,
        private array $extraHeaders = [],
        private array $extraOpts = [],
    ) {
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function getExtraHeaders(): array
    {
        return $this->extraHeaders;
    }

    public function getExtraOpts(): array
    {
        return $this->extraOpts;
    }
}
