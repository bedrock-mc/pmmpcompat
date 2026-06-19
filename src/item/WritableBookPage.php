<?php

declare(strict_types=1);

namespace pocketmine\item;

class WritableBookPage extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:writablebookpage', 'WritableBookPage'); }
    public const PAGE_LENGTH_HARD_LIMIT_BYTES = 0;
    public const PHOTO_NAME_LENGTH_HARD_LIMIT_BYTES = 0;
    public function getPhotoName(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getText(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
