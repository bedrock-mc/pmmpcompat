<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

abstract class WorldProviderManagerEntry
{
    protected function __construct(protected \Closure $isValid) {}

    abstract public function fromPath(string $path, \Logger $logger): WorldProvider;
    public function isValid(string $path): bool { return (bool) ($this->isValid)($path); }
}
