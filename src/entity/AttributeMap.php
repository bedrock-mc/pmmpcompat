<?php

declare(strict_types=1);

namespace pocketmine\entity;

class AttributeMap
{
    /** @var array<string, Attribute> */
    private array $attributes = [];

    public function add(Attribute $attribute): void { $this->attributes[$attribute->getId()] = $attribute; }
    public function get(string $id): ?Attribute { return $this->attributes[$id] ?? null; }
    /** @return array<string, Attribute> */
    public function getAll(): array { return $this->attributes; }
    /** @return array<string, Attribute> */
    public function needSend(): array
    {
        return array_filter($this->attributes, static fn(Attribute $attribute): bool => $attribute->isSyncable() && $attribute->isDesynchronized());
    }
}
