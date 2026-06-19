<?php

declare(strict_types=1);

namespace pocketmine\wizard;

use pocketmine\Server;

class SetupWizard
{
    public const DEFAULT_NAME = Server::DEFAULT_SERVER_NAME;
    public const DEFAULT_PORT = Server::DEFAULT_PORT_IPV4;
    public const DEFAULT_PLAYERS = Server::DEFAULT_MAX_PLAYERS;

    public function __construct(private string $dataPath) {}

    public function run(): bool
    {
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0777, true);
        }
        return true;
    }
}
