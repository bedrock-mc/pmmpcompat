<?php

declare(strict_types=1);

namespace pocketmine\plugin;

interface ResourceProvider
{
    public function getResource(string $filename);
    public function getResources(): array;
}
