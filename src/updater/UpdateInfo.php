<?php

declare(strict_types=1);

namespace pocketmine\updater;

final class UpdateInfo
{
    public string $php_version = PHP_VERSION;
    public string $base_version = '';
    public bool $is_dev = false;
    public string $channel = 'stable';
    public string $git_commit = '';
    public string $mcpe_version = '';
    public int $build = 0;
    public int $date = 0;
    public string $details_url = '';
    public string $download_url = '';
    public string $source_url = '';
}
