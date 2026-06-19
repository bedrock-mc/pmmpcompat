<?php

declare(strict_types=1);

namespace pocketmine\form;

use pocketmine\player\Player;

interface Form extends \JsonSerializable
{
    public function handleResponse(Player $player, mixed $data): void;
}
