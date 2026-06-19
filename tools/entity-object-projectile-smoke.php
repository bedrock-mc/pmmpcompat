<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\object\EndCrystal;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\FallingBlock;
use pocketmine\entity\object\FireworkRocket;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\object\Painting;
use pocketmine\entity\object\PaintingMotive;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\Egg;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\entity\projectile\ExperienceBottle;
use pocketmine\entity\projectile\IceBomb;
use pocketmine\entity\projectile\Projectile;
use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\entity\projectile\Throwable;
use pocketmine\entity\projectile\Trident;
use pocketmine\item\FireworkRocketExplosion;
use pocketmine\item\Item;
use pocketmine\item\PotionType;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ShortTag;

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

$owner = new Entity();
$projectileNbt = new CompoundTag(['damage' => new DoubleTag(4.5)]);
$projectile = new Snowball(new Location(1, 2, 3, null, 90, 10), $owner, $projectileNbt);
assertSame($owner, $projectile->getOwningEntity(), 'projectile owner storage');
assertSame(5, $projectile->getResultDamage(), 'projectile NBT damage storage');
$projectile->setMotion(new Vector3(0.5, 1.0, 1.5));
assertSame(1.0, $projectile->getMotion()->y, 'projectile motion storage');

$arrow = new Arrow(new Location(0, 64, 0, null, 0, 0), $owner, true);
assertSame('minecraft:arrow', Arrow::getNetworkTypeId(), 'arrow network id');
assertSame(true, $arrow->isCritical(), 'arrow critical constructor storage');
$arrow->setCritical(false);
$arrow->setPickupMode(Arrow::PICKUP_CREATIVE);
$arrow->setPunchKnockback(1.25);
assertSame(false, $arrow->isCritical(), 'arrow critical setter');
assertSame(Arrow::PICKUP_CREATIVE, $arrow->getPickupMode(), 'arrow pickup mode setter');
assertSame(1.25, $arrow->getPunchKnockback(), 'arrow punch knockback setter');
assertSame(true, $arrow->saveNBT()->getTag(Arrow::TAG_CRIT) instanceof ByteTag, 'arrow NBT critical tag');

$pearl = new EnderPearl();
$pearl->onHit();
assertSame(true, $pearl->hasHit(), 'ender pearl hit hook records local state');
assertSame(true, $pearl->isFlaggedForDespawn(), 'ender pearl hit hook flags local despawn only');

$bottle = new ExperienceBottle();
assertSame(-1, $bottle->getResultDamage(), 'experience bottle no damage');
$bottle->onHit(7);
assertSame(7, $bottle->getExperienceDrop(), 'experience bottle local drop amount');

$iceBomb = new IceBomb();
$iceBomb->onHit();
assertSame(true, $iceBomb->hasHit(), 'ice bomb local hit state');

$splashPotion = new SplashPotion(new Location(0, 65, 0, null, 0, 0), $owner, PotionType::STUB);
assertSame('minecraft:splash_potion', SplashPotion::getNetworkTypeId(), 'splash potion network id');
assertSame(PotionType::STUB, $splashPotion->getPotionType(), 'splash potion type storage');
assertSame([], $splashPotion->getPotionEffects(), 'splash potion empty local effects');
$splashPotion->setLinger();
assertSame(true, $splashPotion->willLinger(), 'splash potion linger storage');

$tridentItem = new Item('minecraft:trident', 'Trident');
$trident = new Trident(new Location(0, 65, 0, null, 0, 0), $tridentItem, $owner);
assertSame('minecraft:thrown_trident', Trident::getNetworkTypeId(), 'trident network id');
assertSame('minecraft:trident', $trident->getItem()->getTypeId(), 'trident item storage');
$trident->setCanCollide(false);
assertSame(false, $trident->canCollideWith(new Entity()), 'trident local collision toggle');

$itemEntity = new ItemEntity(new Location(2, 3, 4, null, 0, 0), new Item('minecraft:diamond', 'Diamond', 2));
$itemEntity->setOwner('owner-xuid');
$itemEntity->setThrower('thrower-xuid');
$itemEntity->setPickupDelay(5);
assertSame('minecraft:diamond', $itemEntity->getItem()->getTypeId(), 'item entity item storage');
assertSame('owner-xuid', $itemEntity->getOwner(), 'item entity owner storage');
assertSame('thrower-xuid', $itemEntity->getThrower(), 'item entity thrower storage');
assertSame(5, $itemEntity->getPickupDelay(), 'item entity pickup delay storage');

$crystal = new EndCrystal();
$crystal->setShowBase(true);
$crystal->setBeamTarget(new Vector3(5, 6, 7));
assertSame('minecraft:ender_crystal', EndCrystal::getNetworkTypeId(), 'end crystal network id');
assertSame(true, $crystal->showBase(), 'end crystal show base storage');
assertSame(6.0, $crystal->getBeamTarget()?->y, 'end crystal beam target storage');
$crystal->explode();
assertSame(true, $crystal->hasExploded(), 'end crystal explode hook records local state');

$xpOrb = new ExperienceOrb(new Location(0, 66, 0, null, 0, 0), 42);
assertSame('minecraft:xp_orb', ExperienceOrb::getNetworkTypeId(), 'xp orb network id');
assertSame(42, $xpOrb->getXpValue(), 'xp orb value constructor storage');
assertSame([7, 3, 1], ExperienceOrb::splitIntoOrbSizes(11), 'xp orb split sizes');
$xpOrb->setDespawnDelay(ExperienceOrb::NEVER_DESPAWN);
assertSame(ExperienceOrb::NEVER_DESPAWN, $xpOrb->getDespawnDelay(), 'xp orb despawn delay storage');
assertSame(true, $xpOrb->saveNBT()->getTag(ExperienceOrb::TAG_VALUE_PC) instanceof ShortTag, 'xp orb NBT value tag');

$falling = new FallingBlock(new Location(0, 70, 0, null, 0, 0), VanillaBlocks::STONE());
assertSame('minecraft:falling_block', FallingBlock::getNetworkTypeId(), 'falling block network id');
assertSame('minecraft:stone', $falling->getBlock()->getTypeId(), 'falling block block storage');
assertSame(false, $falling->canCollideWith(new Entity()), 'falling block collision facade');

$firework = new FireworkRocket(new Location(0, 70, 0, null, 0, 0), 12, [new FireworkRocketExplosion()]);
assertSame('minecraft:fireworks_rocket', FireworkRocket::getNetworkTypeId(), 'firework network id');
assertSame(12, $firework->getMaxFlightTimeTicks(), 'firework max flight time storage');
assertSame(1, count($firework->getExplosions()), 'firework explosion list storage');
$firework->explode();
assertSame(true, $firework->hasExploded(), 'firework explode hook records local state');

$painting = new Painting(new Location(0, 70, 0, null, 0, 0), new Vector3(1, 2, 3), 2, $alban);
assertSame('minecraft:painting', Painting::getNetworkTypeId(), 'painting network id');
assertSame(2, $painting->getFacing(), 'painting facing storage');
assertSame($alban, $painting->getMotive(), 'painting motive storage');
assertSame('Alban', $painting->saveNBT()->getString(Painting::TAG_MOTIVE), 'painting NBT motive tag');

$tnt = new PrimedTNT(new Location(0, 70, 0, null, 0, 0));
$tnt->setFuse(40);
$tnt->setWorksUnderwater(true);
assertSame('minecraft:tnt', PrimedTNT::getNetworkTypeId(), 'primed TNT network id');
assertSame(40, $tnt->getFuse(), 'primed TNT fuse storage');
assertSame(true, $tnt->worksUnderwater(), 'primed TNT underwater storage');
$tnt->explode();
assertSame(true, $tnt->hasExploded(), 'primed TNT explode hook records local state');

echo "pmmpcompat entity object/projectile smoke ok\n";

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}
