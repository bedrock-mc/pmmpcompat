<?php

declare(strict_types=1);

namespace pocketmine\updater;

use pocketmine\Server;
use pocketmine\utils\VersionString;
use pocketmine\VersionInfo;

class UpdateChecker
{
    protected ?UpdateInfo $updateInfo = null;
    protected string $endpoint;

    public function __construct(protected Server $server, string $endpoint = 'update.pmmp.io')
    {
        $this->endpoint = str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://') ? rtrim($endpoint, '/') . '/' : 'http://' . $endpoint . '/api/';
    }

    public function checkUpdateError(string $error): void
    {
        $this->server->getLogger()->debug('Async update check failed due to "' . $error . '"', true);
    }

    public function checkUpdateCallback(UpdateInfo $updateInfo): void
    {
        $this->checkUpdate($updateInfo);
    }

    public function hasUpdate(): bool
    {
        return $this->updateInfo !== null;
    }

    public function showConsoleUpdate(): void
    {
        if ($this->updateInfo === null) {
            return;
        }
        $this->server->getLogger()->warning('Update available: ' . $this->updateInfo->base_version);
    }

    public function getUpdateInfo(): ?UpdateInfo
    {
        return $this->updateInfo;
    }

    public function doCheck(): void
    {
        $this->checkUpdateError('Network update checks are disabled by pmmpcompat');
    }

    protected function checkUpdate(UpdateInfo $updateInfo): void
    {
        try {
            $newVersion = new VersionString($updateInfo->base_version, $updateInfo->is_dev, $updateInfo->build);
        } catch (\InvalidArgumentException) {
            return;
        }
        if (VersionInfo::VERSION()->compare($newVersion) > 0) {
            $this->updateInfo = $updateInfo;
        }
    }

    public function getChannel(): string
    {
        return 'stable';
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
