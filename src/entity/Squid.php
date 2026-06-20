<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageEvent;

class Squid extends WaterAnimal
{
    public function attack(EntityDamageEvent $source): void { parent::attack($source); }
    public function getDrops(mixed ...$args): array { return []; }
    public function getName(mixed ...$args): string { return 'Squid'; }
    public static function getNetworkTypeId(mixed ...$args): mixed { return 'minecraft:squid'; }
    public function getPickedItem(mixed ...$args): mixed { return null; }
    public function initEntity(mixed ...$args): mixed { return null; }
}
