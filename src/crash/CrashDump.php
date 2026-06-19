<?php

declare(strict_types=1);

namespace pocketmine\crash;

use pocketmine\Server;
use pocketmine\VersionInfo;

class CrashDump
{
    public const PLUGIN_INVOLVEMENT_NONE = 'none';
    public const PLUGIN_INVOLVEMENT_DIRECT = 'direct';
    public const PLUGIN_INVOLVEMENT_INDIRECT = 'indirect';
    public const FATAL_ERROR_MASK = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;

    private CrashDumpData $data;
    private string $encodedData;

    public function __construct(private Server $server, private mixed $pluginManager = null)
    {
        $now = microtime(true);
        $this->data = new CrashDumpData();
        $this->data->format_version = 4;
        $this->data->time = $now;
        $this->data->uptime = $now - $server->getStartTime();
        $this->data->error = error_get_last() ?? [
            'type' => 'Runtime',
            'message' => 'No fatal error captured',
            'file' => __FILE__,
            'line' => __LINE__,
        ];
        $this->data->thread = 'Main';
        $this->data->trace = [];
        $this->data->general = new CrashDumpDataGeneral(
            VersionInfo::NAME,
            VersionInfo::BASE_VERSION,
            VersionInfo::BUILD_NUMBER(),
            VersionInfo::IS_DEVELOPMENT_BUILD,
            0,
            VersionInfo::GIT_HASH(),
            php_uname(),
            PHP_VERSION,
            zend_version(),
            PHP_OS,
            PHP_OS_FAMILY,
            [],
        );
        $this->encodedData = zlib_encode(json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), ZLIB_ENCODING_DEFLATE, 9) ?: '';
    }

    public function getEncodedData(): string
    {
        return $this->encodedData;
    }

    public function getData(): CrashDumpData
    {
        return $this->data;
    }

    public function encodeData(CrashDumpRenderer $renderer): void
    {
        $renderer->addLine();
        $renderer->addLine('----------------------REPORT THE DATA BELOW THIS LINE-----------------------');
        $renderer->addLine();
        $renderer->addLine('===BEGIN CRASH DUMP===');
        foreach (str_split(base64_encode($this->encodedData), 76) as $line) {
            $renderer->addLine($line);
        }
        $renderer->addLine('===END CRASH DUMP===');
    }
}
