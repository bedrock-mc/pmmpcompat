<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Living;
use pocketmine\entity\object\AreaEffectCloud;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class AreaEffectCloudApplyEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    /** @param Living[] $affectedEntities */
    public function __construct(
        AreaEffectCloud $entity,
        protected array $affectedEntities,
    ) {
        $this->entity = $entity;
    }

    public function getEntity(): AreaEffectCloud
    {
        return $this->entity;
    }

    /** @return Living[] */
    public function getAffectedEntities(): array
    {
        return $this->affectedEntities;
    }
}
