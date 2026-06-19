<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks\json;

final class ManifestDependencyEntry
{
    public string $uuid = '';
    /** @var int[] */
    public array $version = [0, 0, 0];
}
