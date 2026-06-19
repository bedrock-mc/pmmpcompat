<?php

declare(strict_types=1);

namespace pocketmine\crash;

final class CrashDumpDataGeneral
{
    /** @param array<string, string> $composer_libraries */
    public function __construct(
        public string $name,
        public string $base_version,
        public int $build,
        public bool $is_dev,
        public int $protocol,
        public string $git,
        public string $uname,
        public string $php,
        public string $zend,
        public string $php_os,
        public string $os,
        public array $composer_libraries,
    ) {}
}
