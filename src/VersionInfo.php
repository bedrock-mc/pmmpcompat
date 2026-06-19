<?php

declare(strict_types=1);

namespace pocketmine;

use pocketmine\utils\Git;
use pocketmine\utils\VersionString;

final class VersionInfo
{
    public const NAME = 'PocketMine-MP';
    public const BASE_VERSION = '5.37.2';
    public const IS_DEVELOPMENT_BUILD = true;
    public const BUILD_CHANNEL = 'stable';
    public const GITHUB_URL = 'https://github.com/pmmp/PocketMine-MP';
    public const WORLD_DATA_VERSION = 1;
    public const TAG_WORLD_DATA_VERSION = 'PMMPDataVersion';

    private static ?string $gitHash = null;
    private static ?int $buildNumber = null;
    private static ?VersionString $fullVersion = null;

    private function __construct() {}

    public static function GIT_HASH(): string
    {
        return self::$gitHash ??= Git::getRepositoryStatePretty(defined('pocketmine\\PATH') ? PATH : getcwd());
    }

    public static function BUILD_NUMBER(): int
    {
        return self::$buildNumber ??= 0;
    }

    public static function VERSION(): VersionString
    {
        return self::$fullVersion ??= new VersionString(self::BASE_VERSION, self::IS_DEVELOPMENT_BUILD, self::BUILD_NUMBER());
    }
}
