<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

$path = sys_get_temp_dir() . '/pmmpcompat-phar-' . getmypid() . '.phar';
@unlink($path);
$phar = new Phar($path);
$phar['plugin.yml'] = <<<'YAML'
name: PharFixture
main: Fixture\PharPlugin
version: 1.0.0
YAML;
$phar['src/Fixture/PharPlugin.php'] = <<<'PHP'
<?php
namespace Fixture;

class PharPlugin extends \pocketmine\plugin\PluginBase {
    public bool $loaded = false;
    public bool $enabled = false;

    protected function onLoad(): void {
        $this->loaded = true;
    }

    protected function onEnable(): void {
        $this->enabled = true;
        $this->saveDefaultConfig();
        $this->saveResource('nested/message.txt');
    }
}
PHP;
$phar['resources/config.yml'] = "enabled: true\nlimit: 13\n";
$phar['resources/nested/message.txt'] = 'phar resource';
$phar->setStub("<?php __HALT_COMPILER();");

$server = new pocketmine\Server();
$classLoader = new pocketmine\thread\ThreadSafeClassLoader();
$pharLoader = new pocketmine\plugin\PharPluginLoader($classLoader);
assert($pharLoader->canLoadPlugin($path) === true);
assert($pharLoader->getAccessProtocol() === 'phar://');
assert($pharLoader->getPluginDescription($path)?->getName() === 'PharFixture');
$pharLoader->loadPlugin($path);
assert($classLoader->findClass('Fixture\\PharPlugin') === 'phar://' . $path . '/src/Fixture/PharPlugin.php');
$plugin = (new pocketmine\plugin\PluginLoader($server))->loadPhar($path);
$plugin->__pmmpCallLoad();
$plugin->__pmmpCallEnable();

assert($plugin->loaded === true);
assert($plugin->enabled === true);
assert($server->getPluginManager()->getPlugin('PharFixture') === $plugin);
assert($plugin->getConfig()->get('limit') === 13);
assert(is_file($plugin->getDataFolder() . 'nested/message.txt'));
assert(file_get_contents($plugin->getDataFolder() . 'nested/message.txt') === 'phar resource');
$resource = $plugin->getResource('nested/message.txt');
assert(is_resource($resource));
assert(stream_get_contents($resource) === 'phar resource');
fclose($resource);

echo "pmmpcompat phar smoke ok\n";
