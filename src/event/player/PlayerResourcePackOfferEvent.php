<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Event;
use pocketmine\player\PlayerInfo;
use pocketmine\resourcepacks\ResourcePack;

class PlayerResourcePackOfferEvent extends Event
{
    public function __construct(
        private PlayerInfo $playerInfo,
        private array $resourcePacks,
        private array $encryptionKeys,
        private bool $mustAccept,
    ) {}

    public function getPlayerInfo(): PlayerInfo { return $this->playerInfo; }
    public function addResourcePack(ResourcePack $entry, ?string $encryptionKey = null): void
    {
        array_unshift($this->resourcePacks, $entry);
        if ($encryptionKey !== null) {
            $this->encryptionKeys[$entry->getPackId()] = $encryptionKey;
        }
    }
    public function setResourcePacks(array $resourcePacks, array $encryptionKeys): void
    {
        $this->resourcePacks = $resourcePacks;
        $this->encryptionKeys = $encryptionKeys;
    }
    public function getResourcePacks(): array { return $this->resourcePacks; }
    public function getEncryptionKeys(): array { return $this->encryptionKeys; }
    public function setMustAccept(bool $mustAccept): void { $this->mustAccept = $mustAccept; }
    public function mustAccept(): bool { return $this->mustAccept; }
}
