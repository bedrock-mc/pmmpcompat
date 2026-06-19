<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

final class BlockStateUpgrader
{
    /** @var array<int, array<int, BlockStateUpgradeSchema>> */
    private array $upgradeSchemas = [];
    private int $outputVersion = BlockStateData::CURRENT_VERSION;

    /** @param list<BlockStateUpgradeSchema> $upgradeSchemas */
    public function __construct(array $upgradeSchemas)
    {
        foreach ($upgradeSchemas as $schema) {
            $this->addSchema($schema);
        }
    }

    public function addSchema(BlockStateUpgradeSchema $schema): void
    {
        $schemaId = $schema->getSchemaId();
        $versionId = $schema->getVersionId();
        if (isset($this->upgradeSchemas[$versionId][$schemaId])) {
            throw new \InvalidArgumentException('Cannot add two schemas with the same schema ID and version ID');
        }
        $this->upgradeSchemas[$versionId][$schemaId] = $schema;
        ksort($this->upgradeSchemas, SORT_NUMERIC);
        ksort($this->upgradeSchemas[$versionId], SORT_NUMERIC);
        $this->outputVersion = max($this->outputVersion, $versionId);
    }

    public function upgrade(BlockStateData $blockStateData): BlockStateData
    {
        $version = $blockStateData->getVersion();
        $name = $blockStateData->getName();
        $states = $blockStateData->getStates();
        foreach ($this->upgradeSchemas as $resultVersion => $schemaList) {
            if ($version > $resultVersion || (count($schemaList) === 1 && $version === $resultVersion)) {
                continue;
            }
            foreach ($schemaList as $schema) {
                [$name, $states] = $this->applySchema($schema, $name, $states);
            }
        }
        return new BlockStateData($name, $states, $this->outputVersion);
    }

    /** @param array<string, Tag> $states @return array{0: string, 1: array<string, Tag>} */
    private function applySchema(BlockStateUpgradeSchema $schema, string $oldName, array $states): array
    {
        $remapped = $this->applyStateRemapped($schema, $oldName, $states);
        if ($remapped !== null) {
            return $remapped;
        }
        if (isset($schema->renamedIds[$oldName]) && isset($schema->flattenedProperties[$oldName])) {
            throw new \LogicException("Both renamedIds and flattenedProperties are set for $oldName");
        }
        if (isset($schema->renamedIds[$oldName])) {
            $newName = $schema->renamedIds[$oldName];
        } elseif (isset($schema->flattenedProperties[$oldName])) {
            [$newName, $states] = $this->applyPropertyFlattened($schema->flattenedProperties[$oldName], $oldName, $states);
        } else {
            $newName = $oldName;
        }
        foreach ($schema->addedProperties[$oldName] ?? [] as $propertyName => $value) {
            $states[$propertyName] ??= $value;
        }
        foreach ($schema->removedProperties[$oldName] ?? [] as $propertyName) {
            unset($states[$propertyName]);
        }
        foreach ($schema->renamedProperties[$oldName] ?? [] as $oldPropertyName => $newPropertyName) {
            if (isset($states[$oldPropertyName])) {
                $oldValue = $states[$oldPropertyName];
                unset($states[$oldPropertyName]);
                $states[$newPropertyName] = $this->locateNewPropertyValue($schema, $oldName, $oldPropertyName, $oldValue);
            }
        }
        foreach ($schema->remappedPropertyValues[$oldName] ?? [] as $oldPropertyName => $remappedValues) {
            if (isset($states[$oldPropertyName])) {
                $states[$oldPropertyName] = $this->locateNewPropertyValue($schema, $oldName, $oldPropertyName, $states[$oldPropertyName]);
            }
        }
        return [$newName, $states];
    }

    /** @param array<string, Tag> $oldState @return array{0: string, 1: array<string, Tag>}|null */
    private function applyStateRemapped(BlockStateUpgradeSchema $schema, string $oldName, array $oldState): ?array
    {
        foreach ($schema->remappedStates[$oldName] ?? [] as $remap) {
            foreach ($remap->oldState as $key => $value) {
                if (!isset($oldState[$key]) || !self::tagEquals($oldState[$key], $value)) {
                    continue 2;
                }
            }
            if (is_string($remap->newName)) {
                $newName = $remap->newName;
            } else {
                [$newName] = $this->applyPropertyFlattened($remap->newName, $oldName, $oldState);
            }
            $newState = $remap->newState;
            foreach ($remap->copiedState as $stateName) {
                if (isset($oldState[$stateName])) {
                    $newState[$stateName] = $oldState[$stateName];
                }
            }
            return [$newName, $newState];
        }
        return null;
    }

    private function locateNewPropertyValue(BlockStateUpgradeSchema $schema, string $oldName, string $oldPropertyName, Tag $oldValue): Tag
    {
        foreach ($schema->remappedPropertyValues[$oldName][$oldPropertyName] ?? [] as $mappedPair) {
            if (self::tagEquals($mappedPair->old, $oldValue)) {
                return $mappedPair->new;
            }
        }
        return $oldValue;
    }

    /** @param array<string, Tag> $states @return array{0: string, 1: array<string, Tag>} */
    private function applyPropertyFlattened(BlockStateUpgradeSchemaFlattenInfo $flattenInfo, string $oldName, array $states): array
    {
        $flattenedValue = $states[$flattenInfo->flattenedProperty] ?? null;
        $expectedType = $flattenInfo->flattenedPropertyType;
        if ($expectedType === null || !$flattenedValue instanceof $expectedType) {
            return [$oldName, $states];
        }
        $embedKey = match (true) {
            $flattenedValue instanceof StringTag => $flattenedValue->getValue(),
            $flattenedValue instanceof ByteTag, $flattenedValue instanceof IntTag => (string) $flattenedValue->getValue(),
            default => throw new \LogicException('Unsupported flattened property type'),
        };
        $embedValue = $flattenInfo->flattenedValueRemaps[$embedKey] ?? $embedKey;
        unset($states[$flattenInfo->flattenedProperty]);
        return [$flattenInfo->prefix . $embedValue . $flattenInfo->suffix, $states];
    }

    private static function tagEquals(Tag $a, Tag $b): bool
    {
        return get_class($a) === get_class($b) && $a->getValue() === $b->getValue();
    }
}
