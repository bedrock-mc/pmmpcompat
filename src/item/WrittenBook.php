<?php

declare(strict_types=1);

namespace pocketmine\item;

class WrittenBook extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:writtenbook', 'WrittenBook'); }
    public const GENERATION_COPY = 0;
    public const GENERATION_COPY_OF_COPY = 0;
    public const GENERATION_ORIGINAL = 0;
    public const GENERATION_TATTERED = 0;
    public const TAG_AUTHOR = 0;
    public const TAG_GENERATION = 0;
    public const TAG_TITLE = 0;
    public function getAuthor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getGeneration(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMaxStackSize(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getTitle(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setAuthor(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setGeneration(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setTitle(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
