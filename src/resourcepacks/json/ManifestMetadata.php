<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks\json;

final class ManifestMetadata
{
    /** @var string[]|null */
    public ?array $authors = null;
    public ?string $license = null;
    public ?string $url = null;
}
