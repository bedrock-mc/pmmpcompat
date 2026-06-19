<?php

declare(strict_types=1);

namespace pocketmine\player\chat;

use pocketmine\lang\Translatable;

interface ChatFormatter
{
    public function format(string $username, string $message): Translatable|string;
}
