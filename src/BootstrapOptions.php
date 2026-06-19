<?php

declare(strict_types=1);

namespace pocketmine;

final class BootstrapOptions
{
    private function __construct() {}

    public const NO_WIZARD = 'no-wizard';
    public const DISABLE_ANSI = 'disable-ansi';
    public const ENABLE_ANSI = 'enable-ansi';
    public const PLUGINS = 'plugins';
    public const DATA = 'data';
    public const VERSION = 'version';
    public const NO_LOG_FILE = 'no-log-file';
}
