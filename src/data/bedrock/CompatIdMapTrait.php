<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

trait CompatIdMapTrait
{
    /** @var array<int|string, object> */
    private array $idToObject = [];
    /** @var array<int, int|string> */
    private array $objectToId = [];

    public function register(mixed $id, mixed $object): mixed
    {
        return $this->compatRegister($id, $object);
    }

    public function fromId(mixed $id): mixed
    {
        return $this->compatFromId($id);
    }

    public function toId(mixed $object): mixed
    {
        return $this->compatToId($object);
    }

    protected function compatRegister(mixed $id, mixed $object): mixed
    {
        if (!is_object($object)) {
            return null;
        }
        $this->idToObject[$id] = $object;
        $this->objectToId[spl_object_id($object)] = $id;
        return null;
    }

    protected function compatFromId(mixed $id): mixed
    {
        return $this->idToObject[$id] ?? null;
    }

    protected function compatToId(mixed $object): mixed
    {
        if (!is_object($object)) {
            return null;
        }
        return $this->objectToId[spl_object_id($object)] ?? (property_exists($object, 'name') ? strtolower((string) $object->name) : spl_object_id($object));
    }

    protected function seedEnumCases(string $class): void
    {
        if (!enum_exists($class)) {
            return;
        }
        $i = 0;
        foreach ($class::cases() as $case) {
            $this->register(defined(static::class . '::' . $case->name) ? constant(static::class . '::' . $case->name) : $i, $case);
            $i++;
        }
    }
}
