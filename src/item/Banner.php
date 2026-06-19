<?php

declare(strict_types=1);

namespace pocketmine\item;

class Banner extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:banner', 'Banner'); }
    public const TAG_PATTERNS = 0;
    public const TAG_PATTERN_COLOR = 0;
    public const TAG_PATTERN_NAME = 0;
    public function getColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFuelTime(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getPatterns(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setColor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setPatterns(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
