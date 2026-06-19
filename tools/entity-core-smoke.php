<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\ExperienceManager;
use pocketmine\entity\Human;
use pocketmine\entity\HungerManager;
use pocketmine\entity\Villager;
use pocketmine\entity\Zombie;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\world\World;

$world = new World('entity-core-smoke');
$nbt = entityNbt('minecraft:villager', 12.5, 64.0, -3.25, 90.0, 15.0);
$nbt->setInt('Profession', Villager::PROFESSION_LIBRARIAN);

$location = EntityDataHelper::parseLocation($nbt, $world);
assertSame(12.5, $location->x, 'location x');
assertSame(64.0, $location->y, 'location y');
assertSame(-3.25, $location->z, 'location z');
assertSame(90.0, $location->yaw, 'location yaw');
assertSame(15.0, $location->pitch, 'location pitch');
assertSame($world, $location->getWorld(), 'location world');

$factory = EntityFactory::getInstance();
assertSame(true, $factory->isRegistered(Villager::class), 'villager registered');
assertSame('Squid', $factory->getSaveId(pocketmine\entity\Squid::class), 'squid save id');

$villager = $factory->createFromData($world, $nbt);
assertSame(true, $villager instanceof Villager, 'factory creates villager');
assertSame(Villager::PROFESSION_LIBRARIAN, $villager->getProfession(), 'villager profession from nbt');
assertSame('minecraft:villager', Villager::getNetworkTypeId(), 'villager network id');

$inject = new CompoundTag();
$factory->injectSaveId(Zombie::class, $inject);
assertSame('Zombie', $inject->getString(EntityFactory::TAG_IDENTIFIER), 'factory inject save id');

$entity = new Entity($location);
$entity->setNameTag('Compat');
$entity->setFireTicks(40);
$entity->setMotion(1.0, 2.0, 3.0);
assertSame('Compat', $entity->getNameTag(), 'entity name tag storage');
assertSame(true, $entity->isOnFire(), 'entity fire ticks storage');
assertSame(2.0, $entity->getMotion()->y, 'entity motion storage');
assertSame(true, $entity->canSaveWithChunk(), 'entity chunk save default');

$human = new Human($location);
assertSame(true, $human->getHungerManager() instanceof HungerManager, 'human hunger manager');
assertSame(true, $human->getXpManager() instanceof ExperienceManager, 'human xp manager');

$human->getHungerManager()->setFood(10.0);
$human->getHungerManager()->addFood(15.0);
assertSame(20.0, $human->getHungerManager()->getFood(), 'hunger clamps to max');
$human->getHungerManager()->exhaust(4.0);
assertSame(19.0, $human->getHungerManager()->getSaturation(), 'exhaust consumes saturation');

$human->getXpManager()->addXp(352);
assertSame(16, $human->getXpManager()->getXpLevel(), 'xp total reaches level');
assertSame(352, $human->getXpManager()->getLifetimeTotalXp(), 'lifetime xp increments');
$human->getXpManager()->subtractXpLevels(1);
assertSame(15, $human->getXpManager()->getXpLevel(), 'xp level subtraction');

echo "pmmpcompat entity core smoke ok\n";

function entityNbt(string $id, float $x, float $y, float $z, float $yaw, float $pitch): CompoundTag
{
    return CompoundTag::create()
        ->setString(EntityFactory::TAG_IDENTIFIER, $id)
        ->setTag(Entity::TAG_POS, new ListTag([new DoubleTag($x), new DoubleTag($y), new DoubleTag($z)]))
        ->setTag(Entity::TAG_MOTION, new ListTag([new DoubleTag(0.0), new DoubleTag(0.0), new DoubleTag(0.0)]))
        ->setTag(Entity::TAG_ROTATION, new ListTag([new FloatTag($yaw), new FloatTag($pitch)]));
}

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}
