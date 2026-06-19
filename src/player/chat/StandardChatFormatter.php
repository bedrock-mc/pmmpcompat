<?php

declare(strict_types=1);

namespace pocketmine\player\chat;

use pocketmine\lang\Translatable;

final class StandardChatFormatter implements ChatFormatter
{
    public function format(string $username, string $message): Translatable|string
    {
        return new Translatable('chat.type.text', [$username, $message]);
    }
}
