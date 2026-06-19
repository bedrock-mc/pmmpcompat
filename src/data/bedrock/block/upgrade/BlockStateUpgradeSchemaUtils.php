<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;

final class BlockStateUpgradeSchemaUtils
{
    public static function describe(BlockStateUpgradeSchema $schema): string
    {
        return sprintf(
            'BlockStateUpgradeSchema #%d for %d.%d.%d.%d',
            $schema->getSchemaId(),
            $schema->maxVersionMajor,
            $schema->maxVersionMinor,
            $schema->maxVersionPatch,
            $schema->maxVersionRevision
        );
    }

    public static function tagToJsonModel(Tag $tag): object
    {
        return match (true) {
            $tag instanceof ByteTag => (object) ['byte' => $tag->getValue()],
            $tag instanceof IntTag => (object) ['int' => $tag->getValue()],
            $tag instanceof StringTag => (object) ['string' => $tag->getValue()],
            default => throw new \UnexpectedValueException('Unsupported block state tag ' . get_debug_type($tag)),
        };
    }

    public static function toJsonModel(BlockStateUpgradeSchema $schema): object
    {
        return (object) [
            'maxVersionMajor' => $schema->maxVersionMajor,
            'maxVersionMinor' => $schema->maxVersionMinor,
            'maxVersionPatch' => $schema->maxVersionPatch,
            'maxVersionRevision' => $schema->maxVersionRevision,
            'renamedIds' => $schema->renamedIds,
            'addedProperties' => self::encodeTagMap($schema->addedProperties),
            'removedProperties' => $schema->removedProperties,
            'renamedProperties' => $schema->renamedProperties,
        ];
    }

    public static function fromJsonModel(object $model, int $schemaId): BlockStateUpgradeSchema
    {
        $schema = new BlockStateUpgradeSchema(
            (int) ($model->maxVersionMajor ?? 0),
            (int) ($model->maxVersionMinor ?? 0),
            (int) ($model->maxVersionPatch ?? 0),
            (int) ($model->maxVersionRevision ?? 0),
            $schemaId
        );
        $schema->renamedIds = self::stringMap($model->renamedIds ?? []);
        $schema->removedProperties = self::stringListMap($model->removedProperties ?? []);
        $schema->renamedProperties = self::nestedStringMap($model->renamedProperties ?? []);
        $schema->addedProperties = self::decodeAddedProperties($model->addedProperties ?? []);
        $schema->remappedPropertyValues = self::decodeRemappedPropertyValues($model->remappedPropertyValues ?? []);
        $schema->flattenedProperties = self::decodeFlattenedProperties($model->flattenedProperties ?? []);
        $schema->remappedStates = self::decodeRemappedStates($model->remappedStates ?? []);
        return $schema;
    }

    /** @return array<int, BlockStateUpgradeSchema> */
    public static function loadSchemas(string $path, int $maxSchemaId): array
    {
        $result = [];
        $iterator = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $filename => $file) {
            if (!preg_match('/^(\d{4}).*\.json$/', (string) $filename, $matches)) {
                continue;
            }
            $schemaId = (int) $matches[1];
            if ($schemaId > $maxSchemaId) {
                continue;
            }
            $result[$schemaId] = self::loadSchemaFromString((string) file_get_contents($file->getPathname()), $schemaId);
        }
        ksort($result, SORT_NUMERIC);
        return $result;
    }

    public static function loadSchemaFromString(string $raw, int $schemaId): BlockStateUpgradeSchema
    {
        try {
            $json = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }
        if (!is_object($json)) {
            throw new \RuntimeException('Unexpected root type of schema file');
        }
        return self::fromJsonModel($json, $schemaId);
    }

    /** @param array<string, array<string, Tag>> $input */
    private static function encodeTagMap(array $input): object
    {
        $result = [];
        foreach ($input as $blockName => $properties) {
            foreach ($properties as $propertyName => $tag) {
                $result[$blockName][$propertyName] = self::tagToJsonModel($tag);
            }
        }
        return (object) $result;
    }

    private static function jsonModelToTag(mixed $model): Tag
    {
        if (is_array($model)) {
            $model = (object) $model;
        }
        if (!is_object($model)) {
            throw new \UnexpectedValueException('Malformed block state tag model');
        }
        $set = array_filter([
            'byte' => property_exists($model, 'byte'),
            'int' => property_exists($model, 'int'),
            'string' => property_exists($model, 'string'),
        ]);
        if (count($set) !== 1) {
            throw new \UnexpectedValueException("Expected exactly one of 'byte', 'int' or 'string'");
        }
        if (property_exists($model, 'byte')) {
            return new ByteTag((int) $model->byte);
        }
        if (property_exists($model, 'int')) {
            return new IntTag((int) $model->int);
        }
        return new StringTag((string) $model->string);
    }

    /** @return array<string, string> */
    private static function stringMap(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $key => $value) {
            $result[(string) $key] = (string) $value;
        }
        return $result;
    }

    /** @return array<string, list<string>> */
    private static function stringListMap(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $key => $values) {
            $result[(string) $key] = array_values(array_map('strval', (array) $values));
        }
        return $result;
    }

    /** @return array<string, array<string, string>> */
    private static function nestedStringMap(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $key => $values) {
            $result[(string) $key] = self::stringMap($values);
        }
        return $result;
    }

    /** @return array<string, array<string, Tag>> */
    private static function decodeAddedProperties(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $blockName => $properties) {
            foreach ((array) $properties as $propertyName => $value) {
                $result[(string) $blockName][(string) $propertyName] = self::jsonModelToTag($value);
            }
        }
        return $result;
    }

    /** @return array<string, array<string, list<BlockStateUpgradeSchemaValueRemap>>> */
    private static function decodeRemappedPropertyValues(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $blockName => $properties) {
            foreach ((array) $properties as $propertyName => $pairs) {
                foreach ((array) $pairs as $pair) {
                    if (is_array($pair)) {
                        $pair = (object) $pair;
                    }
                    if (!is_object($pair) || !property_exists($pair, 'old') || !property_exists($pair, 'new')) {
                        throw new \UnexpectedValueException('Malformed remapped property value');
                    }
                    $result[(string) $blockName][(string) $propertyName][] = new BlockStateUpgradeSchemaValueRemap(
                        self::jsonModelToTag($pair->old),
                        self::jsonModelToTag($pair->new)
                    );
                }
            }
        }
        return $result;
    }

    /** @return array<string, BlockStateUpgradeSchemaFlattenInfo> */
    private static function decodeFlattenedProperties(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $blockName => $rule) {
            if (is_array($rule)) {
                $rule = (object) $rule;
            }
            $result[(string) $blockName] = new BlockStateUpgradeSchemaFlattenInfo(
                (string) ($rule->prefix ?? ''),
                (string) ($rule->flattenedProperty ?? ''),
                self::tagClassName($rule->flattenedPropertyType ?? null),
                (string) ($rule->suffix ?? ''),
                self::stringMap($rule->flattenedValueRemaps ?? [])
            );
        }
        return $result;
    }

    /** @return array<string, list<BlockStateUpgradeSchemaBlockRemap>> */
    private static function decodeRemappedStates(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $blockName => $remaps) {
            foreach ((array) $remaps as $remap) {
                if (is_array($remap)) {
                    $remap = (object) $remap;
                }
                $result[(string) $blockName][] = new BlockStateUpgradeSchemaBlockRemap(
                    self::decodeTagListMap($remap->oldState ?? []),
                    isset($remap->newFlattenedName) ? self::decodeFlattenedProperties(['_' => $remap->newFlattenedName])['_'] : (string) ($remap->newName ?? $blockName),
                    self::decodeTagListMap($remap->newState ?? []),
                    array_values(array_map('strval', (array) ($remap->copiedState ?? [])))
                );
            }
        }
        return $result;
    }

    /** @return array<string, Tag> */
    private static function decodeTagListMap(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $key => $value) {
            $result[(string) $key] = self::jsonModelToTag($value);
        }
        return $result;
    }

    /** @return class-string<Tag>|null */
    private static function tagClassName(mixed $type): ?string
    {
        return match ((string) $type) {
            '', 'null' => null,
            'byte', ByteTag::class => ByteTag::class,
            'int', IntTag::class => IntTag::class,
            'string', StringTag::class => StringTag::class,
            default => throw new \UnexpectedValueException('Unknown flattened property tag type ' . (string) $type),
        };
    }
}
