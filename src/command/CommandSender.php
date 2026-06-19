<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\lang\Translatable;
use pocketmine\Server;

interface CommandSender
{
    public function getLanguage(): mixed;
    public function sendMessage(Translatable|string $message): void;
    public function getServer(): Server;
    public function getName(): string;
    public function getScreenLineHeight(): int;
    public function setScreenLineHeight(?int $height): void;
    public function hasPermission(string $name): bool;
}
