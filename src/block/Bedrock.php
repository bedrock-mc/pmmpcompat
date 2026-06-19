<?php

declare(strict_types=1);

namespace pocketmine\block;

class Bedrock extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bedrock', 'Bedrock'); }
    public function burnsForever(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function setBurnsForever(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
