<?php

declare(strict_types=1);

namespace pocketmine\compat;

use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\GameMode;
use pocketmine\world\Position;

interface PlayerBridge
{
    public function sendMessage(string $message): void;
    public function sendPopup(string $message): void;
    public function sendTip(string $message): void;
    public function sendActionBarMessage(string $message): void;
    public function sendTitle(string $title, string $subtitle): void;
    public function setTitleDuration(int $fadeIn, int $stay, int $fadeOut): void;
    public function resetTitles(): void;
    public function removeTitles(): void;
    public function teleport(Position $position): void;
    public function kick(string $reason): void;
    public function transfer(string $address, int $port, string $message): void;
    public function sendForm(int $formId, Form $form): void;
    public function setGamemode(GameMode $gamemode): void;
    public function setHealth(float $health, float $maxHealth): void;
    public function setExperience(int $level, float $progress): void;
    public function setAllowFlight(bool $value): void;
    public function setFlying(bool $value): void;
    public function setFlightSpeedMultiplier(float $value): void;
    public function setViewDistance(int $distance): void;
    public function setInventoryItem(int $slot, Item $item): void;
    public function clearInventorySlot(int $slot): void;
    public function clearInventory(): void;
}
