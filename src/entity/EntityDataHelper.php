<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\data\SavedDataLoadingException;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ScalarTag;
use pocketmine\world\World;
use function count;
use function is_finite;

class EntityDataHelper
{
    public static function parseLocation(CompoundTag $nbt, ?World $world): Location
    {
        $pos = self::parseVec3($nbt, Entity::TAG_POS, false);
        $rotation = self::readList($nbt, Entity::TAG_ROTATION, false, 2);

        return Location::fromObject(
            $pos,
            $world,
            self::readNumber($rotation[0], Entity::TAG_ROTATION, 'yaw'),
            self::readNumber($rotation[1], Entity::TAG_ROTATION, 'pitch'),
        );
    }

    public static function parseVec3(CompoundTag $nbt, string $tagName, bool $optional): Vector3
    {
        if ($nbt->getTag($tagName) === null && $optional) {
            return Vector3::zero();
        }

        $values = self::readList($nbt, $tagName, $optional, 3);
        return new Vector3(
            self::readNumber($values[0], $tagName, 'x'),
            self::readNumber($values[1], $tagName, 'y'),
            self::readNumber($values[2], $tagName, 'z'),
        );
    }

    /**
     * @return list<ScalarTag>
     */
    private static function readList(CompoundTag $nbt, string $tagName, bool $optional, int $expectedCount): array
    {
        $tag = $nbt->getTag($tagName);
        if ($tag === null && $optional) {
            return [];
        }
        if (!$tag instanceof ListTag) {
            throw new SavedDataLoadingException("'" . $tagName . "' should be a List<Double> or List<Float>");
        }

        $values = $tag->getValue();
        if (count($values) !== $expectedCount) {
            throw new SavedDataLoadingException("Expected exactly " . $expectedCount . " entries in '" . $tagName . "' tag");
        }
        foreach ($values as $value) {
            if (!$value instanceof DoubleTag && !$value instanceof FloatTag) {
                throw new SavedDataLoadingException("'" . $tagName . "' should be a List<Double> or List<Float>");
            }
        }

        return $values;
    }

    private static function readNumber(ScalarTag $tag, string $tagName, string $axis): float
    {
        $value = (float) $tag->getValue();
        if (!is_finite($value)) {
            throw new SavedDataLoadingException("'" . $tagName . "' contains non-finite " . $axis . " value");
        }
        return $value;
    }
}
