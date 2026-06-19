<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\utils\Utils;

class EntityDeathEvent extends EntityEvent
{
    /** @param Item[] $drops */
    public function __construct(
        Living $entity,
        private array $drops = [],
        private int $xp = 0,
    ) {
        $this->entity = $entity;
        $this->setDrops($drops);
        $this->setXpDropAmount($xp);
    }

    public function getEntity(): Living { return $this->entity; }
    /** @return Item[] */
    public function getDrops(): array { return $this->drops; }
    /** @param Item[] $drops */
    public function setDrops(array $drops): void
    {
        Utils::validateArrayValueType($drops, fn(Item $_) => null);
        $this->drops = $drops;
    }
    public function getXpDropAmount(): int { return $this->xp; }
    public function setXpDropAmount(int $xp): void
    {
        if ($xp < 0) {
            throw new \InvalidArgumentException('XP drop amount must not be negative');
        }
        $this->xp = $xp;
    }
}
