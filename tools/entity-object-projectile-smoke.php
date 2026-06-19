<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\entity\object\PaintingMotive;
use pocketmine\entity\projectile\Egg;
use pocketmine\entity\projectile\Projectile;
use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\projectile\Throwable;

$motives = PaintingMotive::getAll();
assertSame(true, count($motives) >= 50, 'vanilla painting motive registry populated');

$alban = PaintingMotive::getMotiveByName('Alban');
assertSame(true, $alban instanceof PaintingMotive, 'Alban motive lookup');
assertSame(1, $alban->getWidth(), 'Alban width');
assertSame(1, $alban->getHeight(), 'Alban height');
assertSame('Alban', $alban->getName(), 'Alban name');
assertSame('PaintingMotive(name: Alban, height: 1, width: 1)', (string) $alban, 'Alban string form');

$custom = new PaintingMotive(2, 3, 'CompatCustom');
PaintingMotive::registerMotive($custom);
assertSame($custom, PaintingMotive::getMotiveByName('CompatCustom'), 'custom motive registration');

$snowball = new Snowball();
$egg = new Egg();
assertSame(true, $snowball instanceof Throwable, 'snowball is throwable');
assertSame(true, $snowball instanceof Projectile, 'snowball is projectile');
assertSame(true, $egg instanceof Throwable, 'egg is throwable');
assertSame(true, $egg instanceof Projectile, 'egg is projectile');
assertSame('minecraft:snowball', Snowball::getNetworkTypeId(), 'snowball network id');
assertSame('minecraft:egg', Egg::getNetworkTypeId(), 'egg network id');

$snowball->setBaseDamage(2.25);
assertSame(2.25, $snowball->getBaseDamage(), 'projectile base damage storage');
assertSame(3, $snowball->getResultDamage(), 'projectile result damage ceil');

echo "pmmpcompat entity object/projectile smoke ok\n";

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}
