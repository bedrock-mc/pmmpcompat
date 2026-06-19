<?php

declare(strict_types=1);

namespace pocketmine\entity;

class Zombie extends Living
{
    public function getDrops(mixed ...$args): array { return []; }
    public function getName(mixed ...$args): string { return 'Zombie'; }
    public static function getNetworkTypeId(mixed ...$args): mixed { return 'minecraft:zombie'; }
    public function getPickedItem(mixed ...$args): mixed { return null; }
    public function getXpDropAmount(mixed ...$args): int { return 5; }
}
