<?php

declare(strict_types=1);

namespace pocketmine\world\generator\executor;

final class AsyncGeneratorUnregisterTask
{
    public function __construct(private int $worldId) {}
    public function onRun(): void { ThreadLocalGeneratorContext::unregister($this->worldId); }
}
