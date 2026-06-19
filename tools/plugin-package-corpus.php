<?php
declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\compat\HostActionQueue;
use pocketmine\compat\Runtime;
use pocketmine\Server;

$args = array_values(array_slice($argv, 1));
if ($args === [] || in_array('--help', $args, true) || in_array('-h', $args, true)) {
    fwrite(STDERR, "Usage: php -d phar.readonly=0 tools/plugin-package-corpus.php --self-test|<plugin-folder|plugin.phar|plugins-dir> [...]\n");
    exit($args === [] ? 1 : 0);
}

if ($args === ['--self-test']) {
    $args = createSelfTestCorpus();
}

$results = [];
$failed = false;
foreach ($args as $path) {
    $path = realpath($path) ?: $path;
    try {
        $results[] = runCorpus($path);
    } catch (Throwable $e) {
        $failed = true;
        $results[] = [
            'path' => $path,
            'ok' => false,
            'error' => $e::class . ': ' . $e->getMessage(),
        ];
    }
}

echo json_encode(['ok' => !$failed, 'results' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit($failed ? 1 : 0);

/** @return array<string, mixed> */
function runCorpus(string $path): array
{
    if (!file_exists($path)) {
        throw new RuntimeException("Path does not exist: {$path}");
    }

    $root = sys_get_temp_dir() . '/pmmpcompat-package-corpus-' . getmypid() . '-' . substr(sha1($path), 0, 8);
    removeTree($root);
    mkdir($root . '/plugins', 0777, true);
    installCorpusPath($path, $root . '/plugins');

    $queue = new HostActionQueue();
    $runtime = new Runtime($root . '/plugins', new Server($queue));

    $runtime->load();
    $loaded = array_map(static fn($plugin): string => $plugin->getName(), $runtime->plugins());
    $runtime->enable();
    $enableActions = $queue->drain();
    for ($tick = 1; $tick <= 5; $tick++) {
        $runtime->tick($tick);
    }
    $tickActions = $queue->drain();
    $runtime->disable();
    $disableActions = $queue->drain();

    $actionTypes = [];
    foreach (array_merge($enableActions, $tickActions, $disableActions) as $action) {
        if (isset($action['type']) && is_string($action['type'])) {
            $actionTypes[$action['type']] = true;
        }
    }

    return [
        'path' => $path,
        'ok' => true,
        'plugins' => $loaded,
        'plugin_count' => count($loaded),
        'action_types' => array_values(array_keys($actionTypes)),
    ];
}

function installCorpusPath(string $path, string $pluginsDir): void
{
    if (is_file($path)) {
        if (!str_ends_with(strtolower($path), '.phar')) {
            throw new RuntimeException("Unsupported plugin file: {$path}");
        }
        copy($path, $pluginsDir . DIRECTORY_SEPARATOR . basename($path));
        return;
    }

    if (pluginYmlPath($path) !== null) {
        copyTree($path, $pluginsDir . DIRECTORY_SEPARATOR . basename($path));
        return;
    }

    $installed = 0;
    foreach (scandir($path) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $child = $path . DIRECTORY_SEPARATOR . $entry;
        if (is_dir($child) && pluginYmlPath($child) !== null) {
            copyTree($child, $pluginsDir . DIRECTORY_SEPARATOR . basename($child));
            $installed++;
        } elseif (is_file($child) && str_ends_with(strtolower($child), '.phar')) {
            copy($child, $pluginsDir . DIRECTORY_SEPARATOR . basename($child));
            $installed++;
        }
    }
    if ($installed === 0) {
        throw new RuntimeException("No plugin folders or phars found in: {$path}");
    }
}

/** @return list<string> */
function createSelfTestCorpus(): array
{
    $root = sys_get_temp_dir() . '/pmmpcompat-package-corpus-self-' . getmypid();
    removeTree($root);
    mkdir($root . '/FolderPlugin/src/PackageCorpus/Folder', 0777, true);
    mkdir($root . '/Bundle', 0777, true);

    file_put_contents($root . '/FolderPlugin/plugin.yml', <<<'YAML'
name: FolderPlugin
main: PackageCorpus\Folder\FolderPlugin
version: 1.0.0
YAML);
    file_put_contents($root . '/FolderPlugin/src/PackageCorpus/Folder/FolderPlugin.php', <<<'PHP'
<?php
declare(strict_types=1);
namespace PackageCorpus\Folder;

final class FolderPlugin extends \pocketmine\plugin\PluginBase{
    protected function onEnable() : void{
        $this->getLogger()->info("folder-enabled");
    }
}
PHP);

    if (!class_exists(Phar::class)) {
        throw new RuntimeException('PHP Phar extension is required for --self-test.');
    }
    $pharPath = $root . '/Bundle/PharPackage.phar';
    @unlink($pharPath);
    $phar = new Phar($pharPath);
    $phar['plugin.yml'] = <<<'YAML'
name: PharPackage
main: PackageCorpus\Phar\PharPackage
version: 1.0.0
YAML;
    $phar['src/PackageCorpus/Phar/PharPackage.php'] = <<<'PHP'
<?php
declare(strict_types=1);
namespace PackageCorpus\Phar;

final class PharPackage extends \pocketmine\plugin\PluginBase{
    protected function onEnable() : void{
        $this->getLogger()->info("phar-enabled");
    }
}
PHP;
    $phar->setStub("<?php __HALT_COMPILER();");

    return [$root . '/FolderPlugin', $root . '/Bundle'];
}

function pluginYmlPath(string $path): ?string
{
    foreach (['plugin.yml', 'Plugin.yml', 'plugin.yaml', 'Plugin.yaml'] as $name) {
        $candidate = $path . DIRECTORY_SEPARATOR . $name;
        if (is_file($candidate)) {
            return $candidate;
        }
    }
    return null;
}

function copyTree(string $src, string $dst): void
{
    mkdir($dst, 0777, true);
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($files as $file) {
        $target = $dst . DIRECTORY_SEPARATOR . $files->getSubPathName();
        if ($file->isDir()) {
            mkdir($target, 0777, true);
        } else {
            copy($file->getPathname(), $target);
        }
    }
}

function removeTree(string $path): void
{
    if (!is_dir($path)) {
        return;
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($path);
}
