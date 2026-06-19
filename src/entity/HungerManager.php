<?php

declare(strict_types=1);

namespace pocketmine\entity;

class HungerManager
{
    private Attribute $hungerAttr;
    private Attribute $saturationAttr;
    private Attribute $exhaustionAttr;
    private int $foodTickTimer = 0;
    private bool $enabled = true;

    public function __construct(private Human $entity)
    {
        $this->hungerAttr = self::fetchAttribute($entity, Attribute::HUNGER);
        $this->saturationAttr = self::fetchAttribute($entity, Attribute::SATURATION);
        $this->exhaustionAttr = self::fetchAttribute($entity, Attribute::EXHAUSTION);
    }

    private static function fetchAttribute(Entity $entity, string $attributeId): Attribute
    {
        $attribute = AttributeFactory::getInstance()->mustGet($attributeId);
        $entity->getAttributeMap()->add($attribute);
        return $attribute;
    }

    public function addFood(float $amount): void { $this->setFood($this->getFood() + $amount); }
    public function addSaturation(float $amount): void { $this->setSaturation($this->getSaturation() + $amount); }
    public function exhaust(float $amount, int $cause = 0): float
    {
        if (!$this->enabled) {
            return 0.0;
        }

        $exhaustion = $this->getExhaustion() + $amount;
        while ($exhaustion >= 4.0) {
            $exhaustion -= 4.0;
            if ($this->getSaturation() > 0.0) {
                $this->setSaturation($this->getSaturation() - 1.0);
            } elseif ($this->getFood() > 0.0) {
                $this->setFood($this->getFood() - 1.0);
            }
        }
        $this->setExhaustion($exhaustion);
        return $amount;
    }
    public function getExhaustion(): float { return $this->exhaustionAttr->getValue(); }
    public function getFood(): float { return $this->hungerAttr->getValue(); }
    public function getFoodTickTimer(): int { return $this->foodTickTimer; }
    public function getMaxFood(): float { return $this->hungerAttr->getMaxValue(); }
    public function getSaturation(): float { return $this->saturationAttr->getValue(); }
    public function isEnabled(): bool { return $this->enabled; }
    public function isHungry(): bool { return $this->getFood() < $this->getMaxFood(); }
    public function setEnabled(bool $enabled): void { $this->enabled = $enabled; }
    public function setExhaustion(float $exhaustion): void { $this->exhaustionAttr->setValue($exhaustion, true); }
    public function setFood(float $new): void
    {
        $old = $this->getFood();
        $this->hungerAttr->setValue($new, true);
        foreach ([17.0, 6.0, 0.0] as $bound) {
            if (($old > $bound) !== ($this->getFood() > $bound)) {
                $this->foodTickTimer = 0;
                break;
            }
        }
    }
    public function setFoodTickTimer(int $foodTickTimer): void
    {
        if ($foodTickTimer < 0) {
            throw new \InvalidArgumentException('Expected a non-negative value');
        }
        $this->foodTickTimer = $foodTickTimer;
    }
    public function setSaturation(float $saturation): void { $this->saturationAttr->setValue($saturation, true); }
    public function tick(int $tickDiff = 1): void
    {
        if (!$this->enabled || !$this->entity->isAlive()) {
            return;
        }
        $this->foodTickTimer = ($this->foodTickTimer + $tickDiff) % 80;
    }
}
