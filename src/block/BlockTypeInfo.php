<?php

declare(strict_types=1);

namespace pocketmine\block;

class BlockTypeInfo extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:blocktypeinfo', 'BlockTypeInfo'); }
    public function getBreakInfo(): array { return $this->compatMethod(__FUNCTION__, []); }
    public function getEnchantmentTags(): array { return $this->compatMethod(__FUNCTION__, []); }
    public function getTypeTags(): array { return $this->compatMethod(__FUNCTION__, []); }
    public function hasTypeTag(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
