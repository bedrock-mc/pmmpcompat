<?php

declare(strict_types=1);

use pocketmine\item\enchantment\AvailableEnchantmentRegistry;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemEnchantmentTagRegistry;
use pocketmine\item\enchantment\ItemEnchantmentTags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;

require dirname(__DIR__) . '/autoload.php';

final class SmokeTaggedItem extends Item
{
    /** @param string[] $tags */
    public function __construct(string $typeId, string $name, private array $tags, private bool $enchanted = false)
    {
        parent::__construct($typeId, $name);
    }

    public function getEnchantmentTags(): array
    {
        return $this->tags;
    }

    public function hasEnchantments(mixed ...$args): mixed
    {
        return $this->enchanted;
    }
}

function assert_true(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: $message\n");
        exit(1);
    }
}

$parser = StringToEnchantmentParser::getInstance();
assert_true($parser->parse('sharpness') === VanillaEnchantments::SHARPNESS(), 'sharpness parses to the vanilla singleton');
assert_true($parser->parse('minecraft:fire aspect') === VanillaEnchantments::FIRE_ASPECT(), 'parser normalizes namespace and spaces');
assert_true($parser->parse('definitely_missing') === null, 'unknown enchantment parses to null');

$tagRegistry = ItemEnchantmentTagRegistry::getInstance();
assert_true(in_array(ItemEnchantmentTags::SWORD, $tagRegistry->getNested(ItemEnchantmentTags::WEAPONS), true), 'weapons contains sword');
assert_true($tagRegistry->isTagArrayIntersection([ItemEnchantmentTags::WEAPONS], [ItemEnchantmentTags::AXE]), 'nested weapon tags intersect axe');
assert_true(!$tagRegistry->isTagArrayIntersection([ItemEnchantmentTags::BOW], [ItemEnchantmentTags::BOOTS]), 'unrelated tags do not intersect');

$registry = AvailableEnchantmentRegistry::getInstance();
$sword = new SmokeTaggedItem('minecraft:diamond_sword', 'Diamond Sword', [ItemEnchantmentTags::SWORD]);
$boots = new SmokeTaggedItem('minecraft:diamond_boots', 'Diamond Boots', [ItemEnchantmentTags::BOOTS]);
$enchantedSword = new SmokeTaggedItem('minecraft:diamond_sword', 'Diamond Sword', [ItemEnchantmentTags::SWORD], true);

assert_true($registry->isAvailableForItem(VanillaEnchantments::SHARPNESS(), $sword), 'sharpness is available for swords');
assert_true(!$registry->isAvailableForItem(VanillaEnchantments::SHARPNESS(), $boots), 'sharpness is not available for boots');
assert_true($registry->isAvailableForItem(VanillaEnchantments::FROST_WALKER(), $boots), 'secondary tags allow frost walker on boots');
assert_true($registry->getPrimaryEnchantmentsForItem($enchantedSword) === [], 'primary enchanting rejects already-enchanted items');

$custom = new Enchantment('compat.custom', Rarity::COMMON, 0, 0, 1);
$registry->register($custom, [ItemEnchantmentTags::BOW], []);
assert_true($registry->isRegistered($custom), 'custom enchantment can be registered');
assert_true($registry->isAvailableForItem($custom, new SmokeTaggedItem('minecraft:bow', 'Bow', [ItemEnchantmentTags::BOW])), 'custom tags are consulted');
$registry->unregister($custom);
assert_true(!$registry->isRegistered($custom), 'custom enchantment can be unregistered');

fwrite(STDOUT, "item-enchantment smoke passed\n");
