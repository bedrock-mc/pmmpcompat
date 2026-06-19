<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\command\CommandSender;
use pocketmine\compat\PlayerBridge;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\form\Form;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Player implements CommandSender
{
    public const DEFAULT_FLIGHT_SPEED_MULTIPLIER = 0.05;
    public const TAG_FIRST_PLAYED = 'firstPlayed';
    public const TAG_LAST_KNOWN_XUID = 'lastKnownXuid';
    public const TAG_LAST_PLAYED = 'lastPlayed';
    public const TAG_LEVEL = 'Level';

    /** @var string[] */
    private array $messages = [];
    /** @var array<string, bool> */
    private array $permissions = [];
    /** @var array<int, Form> */
    private array $forms = [];
    private int $nextFormId = 1;
    private Inventory $inventory;
    private ?Position $position = null;
    private float $health = 20.0;
    private float $maxHealth = 20.0;
    private GameMode $gamemode;
    private int $xpLevel = 0;
    private float $xpProgress = 0.0;
    private int $screenLineHeight = 20;
    private string $displayName;
    private bool $connected = true;
    private bool $allowFlight = false;
    private bool $flying = false;
    private bool $autoJump = true;
    private bool $canSaveWithChunk = true;
    private bool $hasBlockCollision = true;
    private bool $usingItem = false;
    private int $viewDistance = 0;
    private float $flightSpeedMultiplier = self::DEFAULT_FLIGHT_SPEED_MULTIPLIER;
    private ?Position $spawn = null;
    private ?Position $deathPosition = null;
    private ?EntityDamageEvent $lastDamageCause = null;
    /** @var array<string, bool> */
    private array $hiddenPlayers = [];
    /** @var array<string, UsedChunkStatus> */
    private array $usedChunks = [];
    private bool $op = false;
    private UuidInterface $uniqueId;

    public function __construct(
        private string $uuid,
        private string $name,
        private ?PlayerBridge $bridge = null,
    ) {
        $this->uniqueId = Uuid::fromString($uuid);
        $this->inventory = new Inventory(36, $bridge);
        $this->gamemode = GameMode::SURVIVAL();
        $this->displayName = $name;
    }

    public function getUniqueId(): UuidInterface
    {
        return $this->uniqueId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $name): void
    {
        $this->displayName = $name;
    }

    public function getXuid(): string
    {
        return $this->uuid;
    }

    public function getLanguage(): mixed
    {
        return null;
    }

    public function sendMessage(Translatable|string $message): void
    {
        $message = $message instanceof Translatable ? $message->getText() : $message;
        $this->messages[] = $message;
        $this->bridge?->sendMessage($message);
    }

    public function getServer(): Server
    {
        return Server::getInstance();
    }

    public function __debugInfo(): array
    {
        return ['uuid' => $this->uuid, 'name' => $this->name, 'position' => $this->position, 'gamemode' => $this->gamemode->getId()];
    }

    public function __destruct() {}

    /** @return string[] */
    public function sentMessages(): array
    {
        return $this->messages;
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getPosition(): Position
    {
        return $this->position ??= new Position(0, 0, 0, new World('world'));
    }

    public function getWorld(): World
    {
        return $this->getPosition()->getWorld();
    }

    public function teleport(mixed $position): void
    {
        if ($position instanceof Position) {
            $this->position = $position;
            $this->bridge?->teleport($position);
        }
    }

    public function disconnect(string $reason = ''): void { $this->kick($reason); }
    public function closeAllForms(): void { $this->forms = []; }

    public function syncPosition(Position $position): void
    {
        $this->position = $position;
    }

    public function getHealth(): float
    {
        return $this->health;
    }

    public function setHealth(float $health): void
    {
        $this->health = max(0.0, min($health, $this->maxHealth));
        $this->bridge?->setHealth($this->health, $this->maxHealth);
    }

    public function getMaxHealth(): float
    {
        return $this->maxHealth;
    }

    public function setMaxHealth(float $maxHealth): void
    {
        $this->maxHealth = max(1.0, $maxHealth);
        $this->health = min($this->health, $this->maxHealth);
        $this->bridge?->setHealth($this->health, $this->maxHealth);
    }

    public function syncHealth(float $health, float $maxHealth): void
    {
        $this->maxHealth = max(1.0, $maxHealth);
        $this->health = max(0.0, min($health, $this->maxHealth));
    }

    public function getLastDamageCause(): ?EntityDamageEvent
    {
        return $this->lastDamageCause;
    }

    public function setLastDamageCause(?EntityDamageEvent $event): void
    {
        $this->lastDamageCause = $event;
    }

    public function getGamemode(): GameMode
    {
        return $this->gamemode;
    }

    public function setGamemode(GameMode $gamemode): bool
    {
        $this->gamemode = $gamemode;
        $this->bridge?->setGamemode($gamemode);
        return true;
    }

    public function syncGamemode(GameMode $gamemode): void
    {
        $this->gamemode = $gamemode;
    }

    public function isSurvival(): bool { return $this->gamemode->equals(GameMode::SURVIVAL()); }
    public function isCreative(): bool { return $this->gamemode->equals(GameMode::CREATIVE()); }
    public function isAdventure(): bool { return $this->gamemode->equals(GameMode::ADVENTURE()); }
    public function isSpectator(): bool { return $this->gamemode->equals(GameMode::SPECTATOR()); }

    public function getXpLevel(): int
    {
        return $this->xpLevel;
    }

    public function setXpLevel(int $level): void
    {
        $this->xpLevel = max(0, $level);
        $this->bridge?->setExperience($this->xpLevel, $this->xpProgress);
    }

    public function getXpProgress(): float
    {
        return $this->xpProgress;
    }

    public function setXpProgress(float $progress): void
    {
        $this->xpProgress = max(0.0, min(1.0, $progress));
        $this->bridge?->setExperience($this->xpLevel, $this->xpProgress);
    }

    public function syncExperience(int $level, float $progress): void
    {
        $this->xpLevel = max(0, $level);
        $this->xpProgress = max(0.0, min(1.0, $progress));
    }

    public function kick(string $reason = ''): void
    {
        $this->connected = false;
        $this->bridge?->kick($reason);
    }

    public function isOnline(): bool { return $this->connected; }
    public function isConnected(): bool { return $this->connected; }
    public function isAuthenticated(): bool { return true; }
    public function hasPlayedBefore(): bool { return false; }
    public function getFirstPlayed(): int { return 0; }
    public function getLastPlayed(): int { return 0; }
    public function getLeaveMessage(): string { return $this->name . ' left the game'; }
    public function getLocale(): string { return 'en_US'; }
    public function getNetworkSession(): mixed { return null; }
    public function getPlayerInfo(): array { return ['uuid' => $this->uuid, 'name' => $this->name, 'xuid' => $this->uuid]; }
    public function getSaveData(): array { return [self::TAG_LEVEL => $this->getWorld()->getFolderName(), self::TAG_LAST_KNOWN_XUID => $this->uuid]; }

    public function sendForm(Form $form): void
    {
        $formId = $this->nextFormId++;
        $this->forms[$formId] = $form;
        $this->bridge?->sendForm($formId, $form);
    }

    public function onFormSubmit(int $formId, mixed $data): bool { return $this->handleFormResponse($formId, $data); }
    public function removeCurrentWindow(): void {}
    public function getCurrentWindow(): mixed { return null; }
    public function setCurrentWindow(mixed $window): void {}
    public function getCraftingGrid(): mixed { return null; }
    public function getCreativeInventory(): Inventory { return $this->inventory; }
    public function setCreativeInventory(mixed $inventory): void {}
    public function getCursorInventory(): Inventory { return $this->inventory; }
    public function consumeHeldItem(): bool { return false; }
    public function selectHotbarSlot(int $slot): void {}

    public function handleFormResponse(int $formId, mixed $data): bool
    {
        $form = $this->forms[$formId] ?? null;
        if ($form === null) {
            return false;
        }
        unset($this->forms[$formId]);
        $form->handleResponse($this, $data);
        return true;
    }

    /** @return array<int, Form> */
    public function sentForms(): array
    {
        return $this->forms;
    }

    public function hasPermission(string $name): bool
    {
        return $this->op || ($this->permissions[strtolower($name)] ?? false);
    }

    public function addPermission(string $name): void
    {
        $this->permissions[strtolower($name)] = true;
    }

    public function removePermission(string $name): void
    {
        unset($this->permissions[strtolower($name)]);
    }

    public function chat(string $message): void { $this->sendMessage($message); }
    public function sendPopup(string $message): void { $this->messages[] = $message; $this->bridge?->sendPopup($message); }
    public function sendTip(string $message): void { $this->messages[] = $message; $this->bridge?->sendTip($message); }
    public function sendActionBarMessage(string $message): void { $this->messages[] = $message; $this->bridge?->sendActionBarMessage($message); }
    public function sendJukeboxPopup(string $message): void { $this->sendMessage($message); }
    public function sendSubTitle(string $subtitle): void { $this->sendTitle('', $subtitle); }
    public function sendTitle(string $title, string $subtitle = ''): void
    {
        $this->messages[] = $subtitle === '' ? $title : $title . "\n" . $subtitle;
        $this->bridge?->sendTitle($title, $subtitle);
    }
    public function sendToastNotification(string $title, string $body): void { $this->sendMessage($title . ': ' . $body); }
    public function setTitleDuration(int $fadeIn, int $stay, int $fadeOut): void { $this->bridge?->setTitleDuration($fadeIn, $stay, $fadeOut); }
    public function resetTitles(): void { $this->bridge?->resetTitles(); }
    public function removeTitles(): void { $this->bridge?->removeTitles(); }
    public function transfer(string $address, int $port = 19132, string $message = ''): bool
    {
        $this->bridge?->transfer($address, $port, $message);
        return $this->bridge !== null;
    }
    public function sendData(mixed ...$args): void {}
    public function sendSkin(mixed ...$args): void {}
    public function changeSkin(mixed ...$args): void {}
    public function emote(string $emoteId): void {}
    public function broadcastAnimation(mixed ...$args): void {}
    public function broadcastSound(mixed ...$args): void {}
    public function getViewers(): array { return []; }

    public function getAllowFlight(): bool { return $this->allowFlight; }
    public function setAllowFlight(bool $value): void { $this->allowFlight = $value; $this->bridge?->setAllowFlight($value); }
    public function isFlying(): bool { return $this->flying; }
    public function setFlying(bool $value): void { $this->flying = $value; $this->bridge?->setFlying($value); }
    public function toggleFlight(bool $value): void { $this->setFlying($value); }
    public function getFlightSpeedMultiplier(): float { return $this->flightSpeedMultiplier; }
    public function setFlightSpeedMultiplier(float $value): void { $this->flightSpeedMultiplier = max(0.0, $value); $this->bridge?->setFlightSpeedMultiplier($this->flightSpeedMultiplier); }
    public function hasAutoJump(): bool { return $this->autoJump; }
    public function setAutoJump(bool $value): void { $this->autoJump = $value; }
    public function canSaveWithChunk(): bool { return $this->canSaveWithChunk; }
    public function setCanSaveWithChunk(bool $value): void { $this->canSaveWithChunk = $value; }
    public function hasBlockCollision(): bool { return $this->hasBlockCollision; }
    public function setHasBlockCollision(bool $value): void { $this->hasBlockCollision = $value; }
    public function getViewDistance(): int { return $this->viewDistance; }
    public function setViewDistance(int $distance): void { $this->viewDistance = max(0, $distance); $this->bridge?->setViewDistance($this->viewDistance); }
    public function hasFiniteResources(): bool { return !$this->isCreative(); }
    public function isSleeping(): bool { return false; }
    public function isUsingItem(): bool { return $this->usingItem; }
    public function setUsingItem(bool $value): void { $this->usingItem = $value; }
    public function isValidUserName(string $name): bool { return $name !== '' && strlen($name) <= 16; }
    public function canBeCollidedWith(): bool { return true; }
    public function canBeMovedByCurrents(): bool { return true; }
    public function canBeRenamed(): bool { return false; }
    public function canBreathe(): bool { return true; }
    public function canCollideWith(mixed $entity): bool { return true; }
    public function canEat(bool $ignoreHunger = false): bool { return true; }
    public function canInteract(Vector3 $pos, float $maxDistance, float $maxDiff = M_SQRT3 / 2): bool { return true; }
    public function canSee(self $player): bool { return !isset($this->hiddenPlayers[$player->getUniqueId()->toString()]); }
    public function hidePlayer(self $player): void { $this->hiddenPlayers[$player->getUniqueId()->toString()] = true; }
    public function showPlayer(self $player): void { unset($this->hiddenPlayers[$player->getUniqueId()->toString()]); }

    public function getSpawn(): ?Position { return $this->spawn; }
    public function setSpawn(?Position $spawn): void { $this->spawn = $spawn; }
    public function hasValidCustomSpawn(): bool { return $this->spawn !== null && $this->spawn->isValid(); }
    public function getDeathPosition(): ?Position { return $this->deathPosition; }
    public function setDeathPosition(?Position $position): void { $this->deathPosition = $position; }
    public function getDrops(): array { return []; }
    public function getXpDropAmount(): int { return 0; }
    public function getInAirTicks(): int { return 0; }
    public function getItemUseDuration(): int { return 0; }
    public function getItemCooldownExpiry(Item $item): int { return 0; }
    public function hasItemCooldown(Item $item): bool { return false; }
    public function resetItemCooldown(Item $item): void {}
    public function dropItem(Item $item): bool { return false; }
    public function pickBlock(mixed ...$args): bool { return false; }
    public function pickEntity(mixed ...$args): bool { return false; }
    public function attack(mixed ...$args): bool { return false; }
    public function attackBlock(mixed ...$args): bool { return false; }
    public function attackEntity(mixed ...$args): bool { return false; }
    public function breakBlock(mixed ...$args): bool { return false; }
    public function continueBreakBlock(mixed ...$args): void {}
    public function stopBreakBlock(): void {}
    public function interactBlock(mixed ...$args): bool { return false; }
    public function interactEntity(mixed ...$args): bool { return false; }
    public function useHeldItem(): bool { return false; }
    public function releaseHeldItem(): bool { return false; }
    public function missSwing(): void {}
    public function jump(): void {}
    public function respawn(): void { $this->connected = true; }
    public function save(): void {}
    public function doChunkRequests(): void {}
    public function doFirstSpawn(): void {}
    public function handleMovement(mixed ...$args): void {}
    public function onChunkChanged(mixed ...$args): void {}
    public function onChunkUnloaded(mixed ...$args): void {}
    public function onPostDisconnect(string $reason = ''): void { $this->connected = false; }
    public function onUpdate(int $currentTick): bool { return true; }
    public function openSignEditor(mixed ...$args): void {}
    public function sleepOn(mixed ...$args): bool { return false; }
    public function stopSleep(): void {}
    public function spawnTo(mixed ...$args): void {}
    public function toggleGlide(bool $value): void {}
    public function toggleSneak(bool $value): void {}
    public function toggleSprint(bool $value): void {}
    public function toggleSwim(bool $value): void {}
    public function setMotion(Vector3 $motion): void {}
    public function getUsedChunkStatus(int $chunkX, int $chunkZ): ?UsedChunkStatus { return $this->usedChunks[World::chunkHash($chunkX, $chunkZ)] ?? null; }
    public function getUsedChunks(): array { return $this->usedChunks; }
    public function hasReceivedChunk(int $chunkX, int $chunkZ): bool { return $this->getUsedChunkStatus($chunkX, $chunkZ) === UsedChunkStatus::SENT; }
    public function isUsingChunk(int $chunkX, int $chunkZ): bool { return isset($this->usedChunks[World::chunkHash($chunkX, $chunkZ)]); }
    public function resetFallDistance(): void {}

    public function isOp(): bool
    {
        return $this->op;
    }

    public function setOp(bool $value): void
    {
        $this->op = $value;
    }

    public function getScreenLineHeight(): int
    {
        return $this->screenLineHeight;
    }

    public function setScreenLineHeight(?int $height): void
    {
        $this->screenLineHeight = max(1, $height ?? 20);
    }
}
