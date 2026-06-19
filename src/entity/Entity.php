<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

class Entity
{
    public const MOTION_THRESHOLD = 0.00001;
    public const TAG_MOTION = 'Motion';
    public const TAG_POS = 'Pos';
    public const TAG_ROTATION = 'Rotation';

    private static int $entityCount = 1;

    protected int $id;
    protected AttributeMap $attributeMap;
    protected ?Location $location = null;
    protected Vector3 $motion;
    protected bool $closed = false;
    protected bool $flaggedForDespawn = false;
    private bool $alive = true;
    private float $health = 20.0;
    private float $maxHealth = 20.0;
    protected float $fallDistance = 0.0;
    protected int $fireTicks = 0;
    protected bool $gravityEnabled = true;
    protected float $gravity = 0.08;
    protected string $nameTag = '';
    protected bool $nameTagVisible = true;
    protected bool $alwaysShowNameTag = false;
    protected string $scoreTag = '';
    protected float $scale = 1.0;
    protected float $stepHeight = 0.0;
    protected bool $canClimb = false;
    protected bool $canClimbWalls = false;
    protected bool $noClientPredictions = false;
    protected bool $invisible = false;
    protected bool $silent = false;
    protected ?int $ownerId = null;
    protected ?int $targetId = null;
    protected bool $savedWithChunk = true;
    protected mixed $lastDamageCause = null;

    public function __construct(?Location $location = null, ?CompoundTag $nbt = null)
    {
        $this->id = self::nextRuntimeId();
        $this->attributeMap = new AttributeMap();
        $this->motion = $nbt !== null ? EntityDataHelper::parseVec3($nbt, self::TAG_MOTION, true) : Vector3::zero();
        $this->location = $location;
        if ($this instanceof NeverSavedWithChunkEntity) {
            $this->savedWithChunk = false;
        }
    }

    public function __destruct() {}
    public function __toString(): string { return static::class . '(' . $this->id . ')'; }
    public function addMotion(mixed ...$args): mixed { $this->motion = $this->motion->add((float) ($args[0] ?? 0.0), (float) ($args[1] ?? 0.0), (float) ($args[2] ?? 0.0)); return null; }
    public function attack(mixed ...$args): mixed { return null; }
    public function broadcastAnimation(mixed ...$args): mixed { return null; }
    public function broadcastSound(mixed ...$args): mixed { return null; }
    public function canBeCollidedWith(mixed ...$args): mixed { return !$this->closed; }
    public function canBeMovedByCurrents(mixed ...$args): mixed { return true; }
    public function canBeRenamed(mixed ...$args): mixed { return true; }
    public function canClimb(mixed ...$args): mixed { return $this->canClimb; }
    public function canClimbWalls(mixed ...$args): mixed { return $this->canClimbWalls; }
    public function canCollideWith(mixed ...$args): mixed { return !$this->closed; }
    public function canSaveWithChunk(mixed ...$args): mixed { return $this->savedWithChunk; }
    public function close(mixed ...$args): mixed { $this->closed = true; $this->alive = false; return null; }
    public function despawnFrom(mixed ...$args): mixed { return null; }
    public function despawnFromAll(mixed ...$args): mixed { return null; }
    public function extinguish(mixed ...$args): mixed { $this->fireTicks = 0; return null; }
    public function flagForDespawn(mixed ...$args): mixed { $this->flaggedForDespawn = true; return null; }
    public function getAttributeMap(mixed ...$args): mixed { return $this->attributeMap; }
    public function getBoundingBox(mixed ...$args): mixed { return null; }
    public function getDirectionPlane(mixed ...$args): mixed { return null; }
    public function getDirectionVector(mixed ...$args): mixed { return null; }
    public function getEyeHeight(mixed ...$args): mixed { return 0.0; }
    public function getEyePos(mixed ...$args): mixed { return $this->location; }
    public function getFallDistance(mixed ...$args): mixed { return $this->fallDistance; }
    public function getFireTicks(mixed ...$args): mixed { return $this->fireTicks; }
    public function getGravity(mixed ...$args): mixed { return $this->gravity; }
    public function getHealth(mixed ...$args): mixed { return $this->health; }
    public function getHorizontalFacing(mixed ...$args): mixed { return null; }
    public function getId(mixed ...$args): mixed { return $this->id; }
    public function getLastDamageCause(mixed ...$args): mixed { return $this->lastDamageCause; }
    public function getLocation(mixed ...$args): mixed { return $this->location; }
    public function getMaxHealth(mixed ...$args): mixed { return $this->maxHealth; }
    public function getMotion(mixed ...$args): mixed { return $this->motion; }
    public function getNameTag(mixed ...$args): mixed { return $this->nameTag; }
    public function getNetworkProperties(mixed ...$args): mixed { return []; }
    public static function getNetworkTypeId(mixed ...$args): mixed { return null; }
    public function getOffsetPosition(mixed ...$args): mixed { return $this->location; }
    public function getOwningEntity(mixed ...$args): mixed { return null; }
    public function getOwningEntityId(mixed ...$args): mixed { return $this->ownerId; }
    public function getPickedItem(mixed ...$args): mixed { return null; }
    public function getPosition(mixed ...$args): mixed { return $this->location; }
    public function getScale(mixed ...$args): mixed { return $this->scale; }
    public function getScoreTag(mixed ...$args): mixed { return $this->scoreTag; }
    public function getSize(mixed ...$args): mixed { return null; }
    public function getStepHeight(mixed ...$args): mixed { return $this->stepHeight; }
    public function getTargetEntity(mixed ...$args): mixed { return null; }
    public function getTargetEntityId(mixed ...$args): mixed { return $this->targetId; }
    public function getViewers(mixed ...$args): mixed { return []; }
    public function getWorld(mixed ...$args): mixed { return $this->location?->isValid() ? $this->location->getWorld() : null; }
    public function hasGravity(mixed ...$args): mixed { return $this->gravityEnabled; }
    public function hasMovementUpdate(mixed ...$args): mixed { return false; }
    public function hasNoClientPredictions(mixed ...$args): mixed { return $this->noClientPredictions; }
    public function heal(mixed ...$args): mixed { $this->setHealth($this->health + (float) ($args[0] ?? 0.0)); return null; }
    public function isAlive(mixed ...$args): mixed { return $this->alive && !$this->closed; }
    public function isClosed(mixed ...$args): mixed { return $this->closed; }
    public function isFireProof(mixed ...$args): mixed { return false; }
    public function isFlaggedForDespawn(mixed ...$args): mixed { return $this->flaggedForDespawn; }
    public function isInsideOfSolid(mixed ...$args): mixed { return false; }
    public function isInvisible(mixed ...$args): mixed { return $this->invisible; }
    public function isNameTagAlwaysVisible(mixed ...$args): mixed { return $this->alwaysShowNameTag; }
    public function isNameTagVisible(mixed ...$args): mixed { return $this->nameTagVisible; }
    public function isOnFire(mixed ...$args): mixed { return $this->fireTicks > 0; }
    public function isOnGround(mixed ...$args): mixed { return false; }
    public function isSilent(mixed ...$args): mixed { return $this->silent; }
    public function isUnderwater(mixed ...$args): mixed { return false; }
    public function kill(mixed ...$args): mixed { $this->alive = false; return null; }
    public static function nextRuntimeId(mixed ...$args): mixed { return self::$entityCount++; }
    public function onCollideWithPlayer(mixed ...$args): mixed { return null; }
    public function onInteract(mixed ...$args): mixed { return false; }
    public function onNearbyBlockChange(mixed ...$args): mixed { return null; }
    public function onRandomUpdate(mixed ...$args): mixed { return null; }
    public function onUpdate(mixed ...$args): mixed { return false; }
    public function resetFallDistance(mixed ...$args): mixed { $this->fallDistance = 0.0; return null; }
    public function respawnToAll(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): mixed
    {
        $nbt = new CompoundTag();
        if ($this->location !== null) {
            $nbt->setTag(self::TAG_POS, new ListTag([
                new DoubleTag($this->location->x),
                new DoubleTag($this->location->y),
                new DoubleTag($this->location->z),
            ]));
            $nbt->setTag(self::TAG_ROTATION, new ListTag([
                new FloatTag($this->location->yaw),
                new FloatTag($this->location->pitch),
            ]));
        }
        return $nbt;
    }
    public function scheduleUpdate(mixed ...$args): mixed { return null; }
    public function sendData(mixed ...$args): mixed { return null; }
    public function setCanClimb(mixed ...$args): mixed { $this->canClimb = (bool) ($args[0] ?? true); return null; }
    public function setCanClimbWalls(mixed ...$args): mixed { $this->canClimbWalls = (bool) ($args[0] ?? true); return null; }
    public function setCanSaveWithChunk(mixed ...$args): mixed { $this->savedWithChunk = (bool) ($args[0] ?? true); return null; }
    public function setFallDistance(mixed ...$args): mixed { $this->fallDistance = max(0.0, (float) ($args[0] ?? 0.0)); return null; }
    public function setFireTicks(mixed ...$args): mixed { $this->fireTicks = max(0, (int) ($args[0] ?? 0)); return null; }
    public function setForceMovementUpdate(mixed ...$args): mixed { return null; }
    public function setGravity(mixed ...$args): mixed { $this->gravity = (float) ($args[0] ?? $this->gravity); return null; }
    public function setHasGravity(mixed ...$args): mixed { $this->gravityEnabled = (bool) ($args[0] ?? true); return null; }
    public function setHealth(mixed ...$args): mixed { $this->health = max(0.0, min((float) ($args[0] ?? $this->health), $this->maxHealth)); return null; }
    public function setInvisible(mixed ...$args): mixed { $this->invisible = (bool) ($args[0] ?? true); return null; }
    public function setLastDamageCause(mixed ...$args): mixed { $this->lastDamageCause = $args[0] ?? null; return null; }
    public function setMaxHealth(mixed ...$args): mixed { $this->maxHealth = max(1.0, (float) ($args[0] ?? $this->maxHealth)); $this->health = min($this->health, $this->maxHealth); return null; }
    public function setMotion(mixed ...$args): mixed { $this->motion = $args[0] instanceof Vector3 ? $args[0] : new Vector3((float) ($args[0] ?? 0.0), (float) ($args[1] ?? 0.0), (float) ($args[2] ?? 0.0)); return null; }
    public function setNameTag(mixed ...$args): mixed { $this->nameTag = (string) ($args[0] ?? ''); return null; }
    public function setNameTagAlwaysVisible(mixed ...$args): mixed { $this->alwaysShowNameTag = (bool) ($args[0] ?? true); return null; }
    public function setNameTagVisible(mixed ...$args): mixed { $this->nameTagVisible = (bool) ($args[0] ?? true); return null; }
    public function setNoClientPredictions(mixed ...$args): mixed { $this->noClientPredictions = (bool) ($args[0] ?? true); return null; }
    public function setOnFire(mixed ...$args): mixed { $this->fireTicks = max($this->fireTicks, (int) (($args[0] ?? 0) * 20)); return null; }
    public function setOwningEntity(mixed ...$args): mixed { $this->ownerId = ($args[0] ?? null) instanceof self ? $args[0]->getId() : null; return null; }
    public function setRotation(mixed ...$args): mixed { if ($this->location !== null) { $this->location->yaw = (float) ($args[0] ?? $this->location->yaw); $this->location->pitch = (float) ($args[1] ?? $this->location->pitch); } return null; }
    public function setScale(mixed ...$args): mixed { $this->scale = max(0.0, (float) ($args[0] ?? $this->scale)); return null; }
    public function setScoreTag(mixed ...$args): mixed { $this->scoreTag = (string) ($args[0] ?? ''); return null; }
    public function setSilent(mixed ...$args): mixed { $this->silent = (bool) ($args[0] ?? true); return null; }
    public function setStepHeight(mixed ...$args): mixed { $this->stepHeight = max(0.0, (float) ($args[0] ?? $this->stepHeight)); return null; }
    public function setTargetEntity(mixed ...$args): mixed { $this->targetId = ($args[0] ?? null) instanceof self ? $args[0]->getId() : null; return null; }
    public function spawnTo(mixed ...$args): mixed { return null; }
    public function spawnToAll(mixed ...$args): mixed { return null; }
    public function teleport(mixed ...$args): mixed { $this->location = $args[0] instanceof Location ? $args[0] : $this->location; return $this->location !== null; }
}
