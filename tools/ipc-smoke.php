<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$plugins = sys_get_temp_dir() . '/pmmpcompat-ipc-plugins-' . getmypid();
@mkdir($plugins . '/EchoPlugin/src/IpcFixture', 0777, true);
file_put_contents($plugins . '/EchoPlugin/plugin.yml', <<<'YAML'
name: EchoPlugin
main: IpcFixture\EchoPlugin
version: 1.0.0
commands:
  echo:
    description: Echo command
YAML);
file_put_contents($plugins . '/EchoPlugin/src/IpcFixture/EchoPlugin.php', <<<'PHP'
<?php
namespace IpcFixture;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\form\SimpleForm;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class EchoPlugin extends PluginBase implements Listener {
    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $event->getPlayer()->sendMessage('welcome ' . $event->getPlayer()->getName());
        $event->getPlayer()->sendForm((new SimpleForm('Greeting', 'Pick one', static function(Player $player, mixed $data): void {
            $player->sendMessage('form response ' . (string) $data);
        }))->addButton('Accept'));
    }

    public function onChat(PlayerChatEvent $event): void {
        $event->setMessage(strtoupper($event->getMessage()));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        $sender->sendMessage('echo ' . implode(' ', $args));
        return true;
    }
}
PHP);

$phpBinary = getenv('PMMPCOMPAT_PHP') ?: PHP_BINARY;
$phpArgs = trim((string) (getenv('PMMPCOMPAT_PHP_ARGS') ?: ''));
$cmdParts = [escapeshellarg($phpBinary)];
if ($phpArgs !== '') {
    foreach (preg_split('/\s+/', $phpArgs) ?: [] as $arg) {
        $cmdParts[] = escapeshellarg($arg);
    }
}
$cmdParts[] = escapeshellarg($root . '/bin/pmmpcompat-runtime.php');
$cmdParts[] = escapeshellarg($plugins);
$cmd = implode(' ', $cmdParts);
$process = proc_open($cmd, [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
], $pipes);
if (!is_resource($process)) {
    throw new RuntimeException('failed to start runtime process');
}
stream_set_blocking($pipes[1], true);

try {
    $load = request($pipes, ['id' => 1, 'type' => 'load']);
    assert($load['ok'] === true);
    assert($load['result']['plugins'] === ['EchoPlugin']);

    $enable = request($pipes, ['id' => 2, 'type' => 'enable']);
    assert($enable['ok'] === true);

    $join = request($pipes, ['id' => 3, 'type' => 'player_join', 'payload' => ['uuid' => 'u1', 'name' => 'Steve']]);
    assert($join['ok'] === true);
    assert($join['actions'][0] === ['type' => 'player.send_message', 'uuid' => 'u1', 'message' => 'welcome Steve']);
    assert($join['actions'][1]['type'] === 'player.send_form');
    assert($join['actions'][1]['uuid'] === 'u1');
    assert($join['actions'][1]['form_id'] === 1);
    assert($join['actions'][1]['form']['type'] === 'form');

    $form = request($pipes, ['id' => 4, 'type' => 'form_response', 'payload' => ['uuid' => 'u1', 'form_id' => 1, 'data' => 0]]);
    assert($form['ok'] === true);
    assert($form['result']['handled'] === true);
    assert($form['actions'][0] === ['type' => 'player.send_message', 'uuid' => 'u1', 'message' => 'form response 0']);

    $chat = request($pipes, ['id' => 5, 'type' => 'chat', 'payload' => ['uuid' => 'u1', 'name' => 'Steve', 'message' => 'hello']]);
    assert($chat['ok'] === true);
    assert($chat['result']['message'] === 'HELLO');

    $command = request($pipes, ['id' => 6, 'type' => 'command', 'payload' => ['uuid' => 'u1', 'name' => 'Steve', 'command' => 'echo', 'args' => ['from', 'ipc']]]);
    assert($command['ok'] === true);
    assert($command['result']['handled'] === true);
    assert($command['actions'][0] === ['type' => 'player.send_message', 'uuid' => 'u1', 'message' => 'echo from ipc']);

    $disable = request($pipes, ['id' => 7, 'type' => 'disable']);
    assert($disable['ok'] === true);
} finally {
    fclose($pipes[0]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    proc_close($process);
}

if ($stderr !== '') {
    fwrite(STDERR, $stderr);
    exit(1);
}

echo "pmmpcompat ipc smoke ok\n";

/**
 * @param array<int, resource> $pipes
 * @param array<string, mixed> $request
 * @return array<string, mixed>
 */
function request(array $pipes, array $request): array
{
    fwrite($pipes[0], json_encode($request) . "\n");
    fflush($pipes[0]);
    $line = fgets($pipes[1]);
    if ($line === false) {
        throw new RuntimeException('runtime process closed stdout');
    }
    $response = json_decode($line, true);
    if (!is_array($response)) {
        throw new RuntimeException('runtime process returned invalid json: ' . $line);
    }
    return $response;
}
