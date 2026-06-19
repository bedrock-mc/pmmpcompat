<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\utils\VersionString;

final class ApiVersion
{
    private function __construct() {}

    /** @param string[] $wantVersionsStr */
    public static function isCompatible(string $myVersionStr, array $wantVersionsStr): bool
    {
        $myVersion = new VersionString($myVersionStr);
        foreach ($wantVersionsStr as $versionStr) {
            $version = new VersionString($versionStr);
            if ($version->getBaseVersion() !== $myVersion->getBaseVersion()) {
                if ($version->getMajor() !== $myVersion->getMajor()) {
                    continue;
                }
                if ($version->getMinor() > $myVersion->getMinor()) {
                    continue;
                }
                if ($version->getMinor() === $myVersion->getMinor() && $version->getPatch() > $myVersion->getPatch()) {
                    continue;
                }
            }
            return true;
        }
        return false;
    }

    /** @param string[] $versions @return string[] */
    public static function checkAmbiguousVersions(array $versions): array
    {
        $indexed = [];
        foreach ($versions as $version) {
            $parsed = new VersionString($version);
            if ($parsed->getSuffix() !== '') {
                continue;
            }
            $indexed[$parsed->getMajor()][] = $parsed;
        }
        $result = [];
        foreach ($indexed as $list) {
            if (count($list) > 1) {
                array_push($result, ...$list);
            }
        }
        usort($result, static fn(VersionString $a, VersionString $b): int => $a->compare($b));
        return array_map(static fn(VersionString $v): string => $v->getBaseVersion(), $result);
    }
}
