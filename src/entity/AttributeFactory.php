<?php

declare(strict_types=1);

namespace pocketmine\entity;

final class AttributeFactory
{
    private static ?self $instance = null;
    /** @var array<string, Attribute> */
    private array $attributes = [];

    public static function getInstance(): self { return self::$instance ??= new self(); }

    public function __construct()
    {
        $this->register(Attribute::ABSORPTION, 0.0, PHP_FLOAT_MAX, 0.0);
        $this->register(Attribute::SATURATION, 0.0, 20.0, 20.0);
        $this->register(Attribute::EXHAUSTION, 0.0, 5.0, 0.0, false);
        $this->register(Attribute::HEALTH, 0.0, 20.0, 20.0);
        $this->register(Attribute::MOVEMENT_SPEED, 0.0, PHP_FLOAT_MAX, 0.1);
        $this->register(Attribute::HUNGER, 0.0, 20.0, 20.0);
        $this->register(Attribute::EXPERIENCE_LEVEL, 0.0, 24791.0, 0.0);
        $this->register(Attribute::EXPERIENCE, 0.0, 1.0, 0.0);
        $this->register(Attribute::LUCK, -1024.0, 1024.0, 0.0);
    }

    public function register(string $id, float $minValue, float $maxValue, float $defaultValue, bool $shouldSend = true): Attribute
    {
        return $this->attributes[$id] = new Attribute($id, $minValue, $maxValue, $defaultValue, $shouldSend);
    }
    public function get(string $id): ?Attribute { return isset($this->attributes[$id]) ? clone $this->attributes[$id] : null; }
    public function mustGet(string $id): Attribute { return $this->get($id) ?? throw new \InvalidArgumentException('Attribute ' . $id . ' is not registered'); }
}
