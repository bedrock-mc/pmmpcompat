<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

use pocketmine\item\enchantment\ItemEnchantmentTags as Tags;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;

final class ItemEnchantmentTagRegistry
{
    use SingletonTrait;

    /** @var array<string, list<string>> */
    private array $tagMap = [];

    private function __construct()
    {
        $this->register(Tags::ARMOR, [Tags::HELMET, Tags::CHESTPLATE, Tags::LEGGINGS, Tags::BOOTS]);
        $this->register(Tags::SHIELD);
        $this->register(Tags::SWORD);
        $this->register(Tags::TRIDENT);
        $this->register(Tags::BOW);
        $this->register(Tags::CROSSBOW);
        $this->register(Tags::SHEARS);
        $this->register(Tags::FLINT_AND_STEEL);
        $this->register(Tags::BLOCK_TOOLS, [Tags::AXE, Tags::PICKAXE, Tags::SHOVEL, Tags::HOE]);
        $this->register(Tags::FISHING_ROD);
        $this->register(Tags::CARROT_ON_STICK);
        $this->register(Tags::COMPASS);
        $this->register(Tags::MASK);
        $this->register(Tags::ELYTRA);
        $this->register(Tags::BRUSH);
        $this->register(Tags::WEAPONS, [
            Tags::SWORD,
            Tags::TRIDENT,
            Tags::BOW,
            Tags::CROSSBOW,
            Tags::BLOCK_TOOLS,
        ]);
    }

    /** @param string[] $nestedTags */
    public function register(string $tag, array $nestedTags = []): void
    {
        $this->assertNotInternalTag($tag);

        foreach ($nestedTags as $nestedTag) {
            if (!isset($this->tagMap[$nestedTag])) {
                $this->register($nestedTag);
            }
            $this->tagMap[$tag][] = $nestedTag;
        }

        if (!isset($this->tagMap[$tag])) {
            $this->tagMap[$tag] = [];
            $this->tagMap[Tags::ALL][] = $tag;
        }
    }

    public function unregister(string $tag): void
    {
        if (!isset($this->tagMap[$tag])) {
            return;
        }
        $this->assertNotInternalTag($tag);

        unset($this->tagMap[$tag]);

        foreach (Utils::stringifyKeys($this->tagMap) as $key => $nestedTags) {
            if (($nestedKey = array_search($tag, $nestedTags, true)) !== false) {
                unset($nestedTags[$nestedKey]);
                $this->tagMap[$key] = array_values($nestedTags);
            }
        }
    }

    /** @param string[] $nestedTags */
    public function removeNested(string $tag, array $nestedTags): void
    {
        $this->assertNotInternalTag($tag);
        $this->tagMap[$tag] = array_values(array_diff($this->tagMap[$tag] ?? [], $nestedTags));
    }

    /** @return string[] */
    public function getNested(string $tag): array
    {
        return $this->tagMap[$tag] ?? [];
    }

    /**
     * @param string[] $firstTags
     * @param string[] $secondTags
     */
    public function isTagArrayIntersection(array $firstTags, array $secondTags): bool
    {
        if (count($firstTags) === 0 || count($secondTags) === 0) {
            return false;
        }

        return count(array_intersect(
            $this->getLeafTagsForArray($firstTags),
            $this->getLeafTagsForArray($secondTags),
        )) !== 0;
    }

    /**
     * @param string[] $tags
     * @return string[]
     */
    private function getLeafTagsForArray(array $tags): array
    {
        $leafTagArrays = [];
        foreach ($tags as $tag) {
            $leafTagArrays[] = $this->getLeafTags($tag);
        }
        return array_unique(array_merge(...$leafTagArrays));
    }

    /** @return string[] */
    private function getLeafTags(string $tag): array
    {
        $result = [];
        $tagsToHandle = [$tag];

        while (count($tagsToHandle) !== 0) {
            $currentTag = array_shift($tagsToHandle);
            $nestedTags = $this->getNested($currentTag);

            if (count($nestedTags) === 0) {
                $result[] = $currentTag;
            } else {
                $tagsToHandle = array_merge($tagsToHandle, $nestedTags);
            }
        }

        return $result;
    }

    private function assertNotInternalTag(string $tag): void
    {
        if ($tag === Tags::ALL) {
            throw new \InvalidArgumentException("Cannot perform any operations on the internal item enchantment tag '$tag'");
        }
    }
}
