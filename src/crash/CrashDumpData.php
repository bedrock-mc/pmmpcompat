<?php

declare(strict_types=1);

namespace pocketmine\crash;

final class CrashDumpData implements \JsonSerializable
{
    public int $format_version = 4;
    public float $time = 0.0;
    public float $uptime = 0.0;
    /** @var mixed[] */
    public array $lastError = [];
    /** @var mixed[] */
    public array $error = [];
    public string $thread = 'Main';
    public string $plugin_involvement = CrashDump::PLUGIN_INVOLVEMENT_NONE;
    public string $plugin = '';
    /** @var array<int, string> */
    public array $code = [];
    /** @var string[] */
    public array $trace = [];
    /** @var array<string, CrashDumpDataPluginEntry> */
    public array $plugins = [];
    /** @var list<string> */
    public array $parameters = [];
    public string $serverDotProperties = '';
    public string $pocketmineDotYml = '';
    /** @var array<string, string> */
    public array $extensions = [];
    public ?int $jit_mode = null;
    public string $phpinfo = '';
    public CrashDumpDataGeneral $general;

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        $result = (array) $this;
        unset($result['serverDotProperties'], $result['pocketmineDotYml']);
        $result['pocketmine.yml'] = $this->pocketmineDotYml;
        $result['server.properties'] = $this->serverDotProperties;
        return $result;
    }
}
