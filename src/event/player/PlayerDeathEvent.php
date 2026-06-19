<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class PlayerDeathEvent extends Event
{
    private Translatable|string $deathMessage;
    private Translatable|string $deathScreenMessage;
    private bool $keepInventory = false;
    private bool $keepXp = false;
    /** @var Item[] */
    private array $drops = [];
    private int $xpDropAmount = 0;

    /** @param Item[] $drops */
    public function __construct(private Player $player, array $drops, int $xp, Translatable|string|null $deathMessage)
    {
        $this->setDrops($drops);
        $this->setXpDropAmount($xp);
        $this->deathMessage = $deathMessage ?? self::deriveMessage($player->getDisplayName(), null);
        $this->deathScreenMessage = $this->deathMessage;
    }

    public function getEntity(): Player { return $this->player; }
    public function getPlayer(): Player { return $this->player; }
    public function getDeathMessage(): Translatable|string { return $this->deathMessage; }
    public function setDeathMessage(Translatable|string $deathMessage): void { $this->deathMessage = $deathMessage; }
    public function getDeathScreenMessage(): Translatable|string { return $this->deathScreenMessage; }
    public function setDeathScreenMessage(Translatable|string $deathScreenMessage): void { $this->deathScreenMessage = $deathScreenMessage; }
    public function getKeepInventory(): bool { return $this->keepInventory; }
    public function setKeepInventory(bool $keepInventory): void { $this->keepInventory = $keepInventory; }
    public function getKeepXp(): bool { return $this->keepXp; }
    public function setKeepXp(bool $keepXp): void { $this->keepXp = $keepXp; }
    public function getDrops(): array { return $this->drops; }
    public function setDrops(array $drops): void
    {
        Utils::validateArrayValueType($drops, fn(Item $_) => null);
        $this->drops = $drops;
    }
    public function getXpDropAmount(): int { return $this->xpDropAmount; }
    public function setXpDropAmount(int $xp): void
    {
        if ($xp < 0) {
            throw new \InvalidArgumentException('XP drop amount must not be negative');
        }
        $this->xpDropAmount = $xp;
    }

    public static function deriveMessage(string $name, ?EntityDamageEvent $deathCause): Translatable
    {
        return new Translatable('death.attack.generic', [$name]);
    }
}
