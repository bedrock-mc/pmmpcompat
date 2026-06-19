<?php

declare(strict_types=1);

namespace pocketmine\console;

use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\Server;

class ConsoleCommandSender implements CommandSender
{
    /** @var string[] */
    private array $messages = [];
    private int $screenLineHeight = 20;

    public function __construct(private Server $server) {}

    public function getLanguage(): mixed
    {
        return null;
    }

    public function sendMessage(Translatable|string $message): void
    {
        $message = $message instanceof Translatable ? $message->getText() : $message;
        $this->messages[] = $message;
        $this->server->getLogger()->info($message);
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getName(): string
    {
        return 'CONSOLE';
    }

    public function hasPermission(string $name): bool
    {
        return true;
    }

    public function getScreenLineHeight(): int
    {
        return $this->screenLineHeight;
    }

    public function setScreenLineHeight(?int $height): void
    {
        $this->screenLineHeight = max(1, $height ?? 20);
    }

    /** @return string[] */
    public function sentMessages(): array
    {
        return $this->messages;
    }
}
