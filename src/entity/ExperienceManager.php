<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\entity\utils\ExperienceUtils;
class ExperienceManager
{
    private const INT32_MAX = 2147483647;

    private Attribute $levelAttr;
    private Attribute $progressAttr;
    private int $totalXp = 0;
    private bool $canAttractXpOrbs = true;
    private int $xpCooldown = 0;

    public function __construct(private Human $entity)
    {
        $this->levelAttr = self::fetchAttribute($entity, Attribute::EXPERIENCE_LEVEL);
        $this->progressAttr = self::fetchAttribute($entity, Attribute::EXPERIENCE);
    }

    private static function fetchAttribute(Entity $entity, string $attributeId): Attribute
    {
        $attribute = AttributeFactory::getInstance()->mustGet($attributeId);
        $entity->getAttributeMap()->add($attribute);
        return $attribute;
    }

    public function addXp(int $amount, bool $playSound = true): bool
    {
        $oldTotal = $this->getCurrentTotalXp();
        if (!$this->setCurrentTotalXp(max(0, $oldTotal + $amount))) {
            return false;
        }
        if ($amount > 0) {
            $this->totalXp = min(self::INT32_MAX, $this->totalXp + $amount);
        }
        return true;
    }
    public function addXpLevels(int $amount, bool $playSound = true): bool { return $this->setXpLevel($this->getXpLevel() + $amount); }
    public function canAttractXpOrbs(): bool { return $this->canAttractXpOrbs; }
    public function canPickupXp(): bool { return $this->xpCooldown === 0; }
    public function getCurrentTotalXp(): int { return ExperienceUtils::getXpToReachLevel($this->getXpLevel()) + $this->getRemainderXp(); }
    public function getLifetimeTotalXp(): int { return $this->totalXp; }
    public function getRemainderXp(): int { return (int) (ExperienceUtils::getXpToCompleteLevel($this->getXpLevel()) * $this->getXpProgress()); }
    public function getXpLevel(): int { return (int) $this->levelAttr->getValue(); }
    public function getXpProgress(): float { return $this->progressAttr->getValue(); }
    public function onPickupXp(int $xpValue): void { $this->xpCooldown = 2; $this->addXp($xpValue); }
    public function resetXpCooldown(): void { $this->xpCooldown = 0; }
    public function setCanAttractXpOrbs(bool $canAttractXpOrbs): void { $this->canAttractXpOrbs = $canAttractXpOrbs; }
    public function setCurrentTotalXp(int $amount): bool
    {
        $newLevel = ExperienceUtils::getLevelFromXp(max(0, $amount));
        $level = (int) $newLevel;
        return $this->setXpAndProgress($level, $newLevel - $level);
    }
    public function setLifetimeTotalXp(int $amount): void
    {
        if ($amount < 0 || $amount > self::INT32_MAX) {
            throw new \InvalidArgumentException('XP must be between 0 and ' . self::INT32_MAX);
        }
        $this->totalXp = $amount;
    }
    public function setXpAndProgress(?int $level, ?float $progress): bool
    {
        if ($level !== null) {
            $this->levelAttr->setValue($level, true);
        }
        if ($progress !== null) {
            $this->progressAttr->setValue($progress, true);
        }
        return true;
    }
    public function setXpAndProgressNoEvent(int $level, float $progress): void { $this->setXpAndProgress($level, $progress); }
    public function setXpLevel(int $level): bool { return $this->setXpAndProgress(max(0, $level), null); }
    public function setXpProgress(float $progress): bool { return $this->setXpAndProgress(null, $progress); }
    public function subtractXp(int $amount): bool { return $this->addXp(-$amount); }
    public function subtractXpLevels(int $amount): bool { return $this->addXpLevels(-$amount); }
    public function tick(int $tickDiff = 1): void { $this->xpCooldown = max(0, $this->xpCooldown - $tickDiff); }
}
