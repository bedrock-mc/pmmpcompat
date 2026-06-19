<?php

declare(strict_types=1);

namespace pocketmine\compat;

use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\GameMode;
use pocketmine\world\Position;

class QueuedPlayerBridge implements PlayerBridge
{
    public function __construct(private string $uuid, private HostActionQueue $queue) {}

    public function sendMessage(string $message): void
    {
        $this->queue->push(['type' => 'player.send_message', 'uuid' => $this->uuid, 'message' => $message]);
    }

    public function sendPopup(string $message): void
    {
        $this->queue->push(['type' => 'player.send_popup', 'uuid' => $this->uuid, 'message' => $message]);
    }

    public function sendTip(string $message): void
    {
        $this->queue->push(['type' => 'player.send_tip', 'uuid' => $this->uuid, 'message' => $message]);
    }

    public function sendActionBarMessage(string $message): void
    {
        $this->queue->push(['type' => 'player.send_actionbar', 'uuid' => $this->uuid, 'message' => $message]);
    }

    public function sendTitle(string $title, string $subtitle): void
    {
        $this->queue->push(['type' => 'player.send_title', 'uuid' => $this->uuid, 'title' => $title, 'subtitle' => $subtitle]);
    }

    public function setTitleDuration(int $fadeIn, int $stay, int $fadeOut): void
    {
        $this->queue->push(['type' => 'player.set_title_duration', 'uuid' => $this->uuid, 'fade_in' => $fadeIn, 'stay' => $stay, 'fade_out' => $fadeOut]);
    }

    public function resetTitles(): void
    {
        $this->queue->push(['type' => 'player.reset_titles', 'uuid' => $this->uuid]);
    }

    public function removeTitles(): void
    {
        $this->queue->push(['type' => 'player.remove_titles', 'uuid' => $this->uuid]);
    }

    public function teleport(Position $position): void
    {
        $this->queue->push([
            'type' => 'player.teleport',
            'uuid' => $this->uuid,
            'position' => [
                'x' => $position->x,
                'y' => $position->y,
                'z' => $position->z,
                'world' => $position->getWorld()->getFolderName(),
            ],
        ]);
    }

    public function kick(string $reason): void
    {
        $this->queue->push(['type' => 'player.kick', 'uuid' => $this->uuid, 'reason' => $reason]);
    }

    public function transfer(string $address, int $port, string $message): void
    {
        $this->queue->push(['type' => 'player.transfer', 'uuid' => $this->uuid, 'address' => $address, 'port' => $port, 'message' => $message]);
    }

    public function sendForm(int $formId, Form $form): void
    {
        $this->queue->push(['type' => 'player.send_form', 'uuid' => $this->uuid, 'form_id' => $formId, 'form' => $form->jsonSerialize()]);
    }

    public function setGamemode(GameMode $gamemode): void
    {
        $this->queue->push(['type' => 'player.set_gamemode', 'uuid' => $this->uuid, 'gamemode' => $gamemode->getId()]);
    }

    public function setHealth(float $health, float $maxHealth): void
    {
        $this->queue->push(['type' => 'player.set_health', 'uuid' => $this->uuid, 'health' => $health, 'max_health' => $maxHealth]);
    }

    public function setExperience(int $level, float $progress): void
    {
        $this->queue->push(['type' => 'player.set_experience', 'uuid' => $this->uuid, 'xp_level' => $level, 'xp_progress' => $progress]);
    }

    public function setAllowFlight(bool $value): void
    {
        $this->queue->push(['type' => 'player.set_allow_flight', 'uuid' => $this->uuid, 'value' => $value]);
    }

    public function setFlying(bool $value): void
    {
        $this->queue->push(['type' => 'player.set_flying', 'uuid' => $this->uuid, 'value' => $value]);
    }

    public function setFlightSpeedMultiplier(float $value): void
    {
        $this->queue->push(['type' => 'player.set_flight_speed', 'uuid' => $this->uuid, 'speed' => $value]);
    }

    public function setViewDistance(int $distance): void
    {
        $this->queue->push(['type' => 'player.set_view_distance', 'uuid' => $this->uuid, 'distance' => $distance]);
    }

    public function setInventoryItem(int $slot, Item $item): void
    {
        $this->queue->push(['type' => 'player.inventory.set_item', 'uuid' => $this->uuid, 'slot' => $slot, 'item' => $item->jsonSerialize()]);
    }

    public function clearInventorySlot(int $slot): void
    {
        $this->queue->push(['type' => 'player.inventory.clear_slot', 'uuid' => $this->uuid, 'slot' => $slot]);
    }

    public function clearInventory(): void
    {
        $this->queue->push(['type' => 'player.inventory.clear', 'uuid' => $this->uuid]);
    }
}
