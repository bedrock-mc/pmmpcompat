<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\utils\TextFormat;

class PlayerInfo
{
    /** @param array<string, mixed> $extraData */
    public function __construct(
        private string $username,
        private mixed $uuid,
        private mixed $skin,
        private string $locale,
        private array $extraData = [],
    ) {
        $this->username = TextFormat::clean($username);
    }

    public function getUsername(): string { return $this->username; }
    public function getUuid(): mixed { return $this->uuid; }
    public function getSkin(): mixed { return $this->skin; }
    public function getLocale(): string { return $this->locale; }
    /** @return array<string, mixed> */
    public function getExtraData(): array { return $this->extraData; }
}
