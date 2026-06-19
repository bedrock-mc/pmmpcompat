<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\inventory\EnchantInventory;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\enchantment\EnchantingOption;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class PlayerEnchantingOptionsRequestEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(Player $player, private EnchantInventory $inventory, private array $options)
    {
        parent::__construct($player);
        $this->setOptions($options);
    }

    public function getInventory(): EnchantInventory { return $this->inventory; }
    public function getOptions(): array { return $this->options; }
    public function setOptions(array $options): void
    {
        Utils::validateArrayValueType($options, fn(EnchantingOption $_) => null);
        if (count($options) > 3) {
            throw new \LogicException('The maximum number of options for an enchanting table is 3');
        }
        $this->options = $options;
    }
}
