<?php

declare(strict_types=1);

namespace pocketmine\utils;

class VersionString
{
    private int $major;
    private int $minor;
    private int $patch;
    private string $suffix;

    public function __construct(
        private string $baseVersion,
        private bool $isDevBuild = false,
        private int $buildNumber = 0,
    ) {
        if (preg_match('/^(\d+)\.(\d+)\.(\d+)(?:-(.*))?$/', $this->baseVersion, $matches) !== 1) {
            throw new \InvalidArgumentException('Invalid base version "' . $baseVersion . '", should contain at least 3 version digits');
        }
        $this->major = (int) $matches[1];
        $this->minor = (int) $matches[2];
        $this->patch = (int) $matches[3];
        $this->suffix = $matches[4] ?? '';
    }

    public static function isValidBaseVersion(string $baseVersion): bool
    {
        return preg_match('/^\d+\.\d+\.\d+(?:-(.*))?$/', $baseVersion) === 1;
    }

    public function getNumber(): int
    {
        return ($this->major * 1_000_000) + ($this->minor * 1_000) + $this->patch;
    }

    public function getBaseVersion(): string
    {
        return $this->baseVersion;
    }

    public function getFullVersion(bool $build = false): string
    {
        $version = $this->baseVersion;
        if ($this->isDevBuild) {
            $version .= '+dev';
            if ($build && $this->buildNumber > 0) {
                $version .= '.' . $this->buildNumber;
            }
        }
        return $version;
    }

    public function getMajor(): int { return $this->major; }
    public function getMinor(): int { return $this->minor; }
    public function getPatch(): int { return $this->patch; }
    public function getSuffix(): string { return $this->suffix; }
    public function getBuild(): int { return $this->buildNumber; }
    public function isDev(): bool { return $this->isDevBuild; }
    public function __toString(): string { return $this->getFullVersion(); }

    public function compare(VersionString $target, bool $diff = false): int
    {
        if ($diff) {
            return $target->getNumber() - $this->getNumber();
        }
        if (($result = $target->getNumber() <=> $this->getNumber()) !== 0) {
            return $result;
        }
        if ($target->isDev() !== $this->isDev()) {
            return $this->isDev() ? 1 : -1;
        }
        if (($target->getSuffix() === '') !== ($this->suffix === '')) {
            return $this->suffix !== '' ? 1 : -1;
        }
        return $target->getBuild() <=> $this->getBuild();
    }
}
