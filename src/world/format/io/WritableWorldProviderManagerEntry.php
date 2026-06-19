<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class WritableWorldProviderManagerEntry extends WorldProviderManagerEntry
{
    public function __construct(\Closure $isValid, private \Closure $fromPath, private \Closure $generate)
    {
        parent::__construct($isValid);
    }

    public function fromPath(string $path, \Logger $logger): WritableWorldProvider
    {
        return ($this->fromPath)($path, $logger);
    }

    public function generate(string $path, string $name, mixed $options): void
    {
        ($this->generate)($path, $name, $options);
    }
}
