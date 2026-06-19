<?php

declare(strict_types=1);

namespace pocketmine\crash;

final class CrashDumpDataPluginEntry
{
    /** @param string[] $authors @param string[] $api @param string[] $depends @param string[] $softDepends */
    public function __construct(
        public string $name,
        public string $version,
        public array $authors,
        public array $api,
        public bool $enabled,
        public array $depends,
        public array $softDepends,
        public string $main,
        public string $load,
        public string $website,
    ) {}
}
