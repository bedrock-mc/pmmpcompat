<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks\json;

final class ManifestHeader
{
    public string $description = '';
    public string $name = '';
    public string $uuid = '';
    /** @var int[] */
    public array $version = [0, 0, 0];
    /** @var int[] */
    public array $min_engine_version = [1, 0, 0];
}
