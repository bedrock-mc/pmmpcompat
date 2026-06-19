<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\entity\Ageable;
use pocketmine\entity\Consumable;
use pocketmine\entity\FoodSource;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\ProjectileSource;
use pocketmine\entity\utils\ExperienceUtils;

assertSame(0, ExperienceUtils::getXpToReachLevel(0), 'level 0 total xp');
assertSame(352, ExperienceUtils::getXpToReachLevel(16), 'level 16 total xp');
assertSame(1507, ExperienceUtils::getXpToReachLevel(31), 'level 31 total xp');
assertSame(7, ExperienceUtils::getXpToCompleteLevel(0), 'level 0 completion xp');
assertSame(37, ExperienceUtils::getXpToCompleteLevel(15), 'level 15 completion xp');
assertSame(112, ExperienceUtils::getXpToCompleteLevel(30), 'level 30 completion xp');
assertNear(16.0, ExperienceUtils::getLevelFromXp(352), 'xp to exact level 16');
assertNear(31.0, ExperienceUtils::getLevelFromXp(1507), 'xp to exact level 31');
assertNear(32.0, ExperienceUtils::getLevelFromXp(1628), 'xp to exact level 32');

$food = new class implements FoodSource {
    public bool $consumed = false;

    public function getAdditionalEffects(): array { return []; }
    public function onConsume(Living $consumer): void { $this->consumed = true; }
    public function getFoodRestore(): int { return 4; }
    public function getSaturationRestore(): float { return 2.4; }
    public function requiresHunger(): bool { return true; }
};
assertSame([], $food->getAdditionalEffects(), 'food additional effects');
assertSame(4, $food->getFoodRestore(), 'food restore');
assertSame(2.4, $food->getSaturationRestore(), 'saturation restore');
assertSame(true, $food->requiresHunger(), 'requires hunger');
assertSame(true, $food instanceof Consumable, 'food extends consumable');

$ageable = new class implements Ageable {
    public function isBaby(): bool { return true; }
};
assertSame(true, $ageable->isBaby(), 'ageable isBaby');

$source = new class implements ProjectileSource {};
assertSame(true, $source instanceof ProjectileSource, 'projectile source marker');

try {
    ExperienceUtils::getLevelFromXp(-1);
    throw new RuntimeException('negative XP did not throw');
} catch (InvalidArgumentException) {
}

echo "pmmpcompat entity smoke ok\n";

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($label . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function assertNear(float $expected, float $actual, string $label): void
{
    if (abs($expected - $actual) > 0.0000001) {
        throw new RuntimeException($label . ': expected near ' . $expected . ', got ' . $actual);
    }
}
