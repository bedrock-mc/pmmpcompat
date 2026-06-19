<?php

declare(strict_types=1);

namespace pocketmine\player\chat;

use pocketmine\lang\Translatable;

final class LegacyRawChatFormatter implements ChatFormatter
{
    public function __construct(private string $format) {}

    public function format(string $username, string $message): Translatable|string
    {
        return str_replace(['{%0}', '{%1}'], [$username, $message], $this->format);
    }
}
