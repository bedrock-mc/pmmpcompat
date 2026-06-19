<?php

declare(strict_types=1);

namespace pocketmine\stats;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\VersionInfo;

class SendUsageTask extends AsyncTask
{
    public const TYPE_OPEN = 1;
    public const TYPE_STATUS = 2;
    public const TYPE_CLOSE = 3;

    public string $endpoint;
    public string $data;

    /** @param string[] $playerList */
    public function __construct(Server $server, int $type, array $playerList = [])
    {
        $event = match ($type) {
            self::TYPE_STATUS => 'status',
            self::TYPE_CLOSE => 'close',
            default => 'open',
        };
        $this->endpoint = 'http://stats.pocketmine.net/api/post';
        $this->data = json_encode([
            'event' => $event,
            'software' => $server->getName(),
            'api' => $server->getApiVersion(),
            'version' => VersionInfo::VERSION()->getFullVersion(true),
            'players' => [
                'count' => count($server->getOnlinePlayers()),
                'limit' => $server->getMaxPlayers(),
                'historyList' => array_values(array_map('md5', $playerList)),
            ],
        ], JSON_THROW_ON_ERROR);
    }

    public function onRun(): void
    {
        // Anonymous telemetry is disabled in pmmpcompat.
    }
}
