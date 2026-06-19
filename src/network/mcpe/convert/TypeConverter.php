<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class TypeConverter
{
    private static ?self $instance = null;
    private SkinAdapter $skinAdapter;

    public function __construct(
        private ?BlockTranslator $blockTranslator = null,
        private ?ItemTranslator $itemTranslator = null,
        private array $itemTypeDictionary = [],
    ) {
        $this->blockTranslator ??= new BlockTranslator();
        $this->itemTranslator ??= new ItemTranslator();
        $this->skinAdapter = new LegacySkinAdapter();
        $this->itemTypeDictionary = $this->itemTypeDictionary !== [] ? $this->itemTypeDictionary : ['minecraft:air' => 0, 'minecraft:stone' => 1, 'minecraft:dirt' => 2];
    }

    public static function getInstance(): self { return self::$instance ??= new self(); }
    public function getBlockTranslator(): BlockTranslator { return $this->blockTranslator; }
    public function getItemTranslator(): ItemTranslator { return $this->itemTranslator; }
    public function getItemTypeDictionary(): array { return $this->itemTypeDictionary; }
    public function getSkinAdapter(): SkinAdapter { return $this->skinAdapter; }
    public function setSkinAdapter(SkinAdapter $skinAdapter): void { $this->skinAdapter = $skinAdapter; }

    public function coreGameModeToProtocol(int $gameMode): int { return $gameMode; }
    public function protocolGameModeToCore(int $gameMode): int { return $gameMode; }
    public function coreItemStackToNet(Item $item): array { return ['id' => $this->itemTranslator->toNetworkId($item), 'count' => $item->getCount(), 'nbt' => $this->itemTranslator->toNetworkNbt($item)]; }
    public function netItemStackToCore(mixed $stack): Item
    {
        if (is_array($stack) && isset($stack['id']) && is_array($stack['id'])) {
            return $this->itemTranslator->fromNetworkId((int) $stack['id'][0], (int) ($stack['id'][1] ?? 0), (int) ($stack['id'][2] ?? 0))->setCount((int) ($stack['count'] ?? 1));
        }
        return VanillaItems::AIR();
    }

    public function coreRecipeIngredientToNet(mixed $ingredient): mixed { return $ingredient; }
    public function netRecipeIngredientToCore(mixed $ingredient): mixed { return $ingredient; }
    public function serializeItemStackExtraData(Item $item): array { return $item->jsonSerialize(); }
    public function deserializeItemStackExtraData(mixed $data): Item { return is_array($data) ? Item::safeNbtDeserialize($data) : VanillaItems::AIR(); }
}
