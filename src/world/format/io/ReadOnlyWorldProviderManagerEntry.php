<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

class ReadOnlyWorldProviderManagerEntry extends WorldProviderManagerEntry
{
    public function __construct(\Closure $isValid, private \Closure $fromPath)
    {
        parent::__construct($isValid);
    }

    public function fromPath(string $path, \Logger $logger): WorldProvider
    {
        return ($this->fromPath)($path, $logger);
    }
}
