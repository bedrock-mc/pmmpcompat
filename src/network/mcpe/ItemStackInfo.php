<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

final class ItemStackInfo
{
    public function __construct(
        private ?int $requestId,
        private int $stackId,
    ) {
    }

    public function getRequestId(): ?int
    {
        return $this->requestId;
    }

    public function getStackId(): int
    {
        return $this->stackId;
    }
}
