<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * General gameplay packet handler facade.
 */
class InGamePacketHandler extends PacketHandler
{
    /** @var array<string, \Closure> */
    private array $callbacks;

    /** @var list<array{name: string, packet: mixed}> */
    private array $gameEvents = [];

    public function __construct(
        private mixed $player = null,
        private mixed $session = null,
        private mixed $inventoryManager = null,
        array $callbacks = []
    ) {
        $this->callbacks = $callbacks;
    }

    public function handleText(mixed $packet): bool
    {
        $message = $this->readField($packet, 'message');
        $type = $this->readField($packet, 'type');
        if (($type === 'chat' || $type === 1 || $type === null) && is_object($this->player) && method_exists($this->player, 'chat')) {
            return (bool) $this->player->chat((string) $message);
        }
        return $this->recordGameEvent('Text', $packet);
    }

    public function handleItemStackRequest(mixed $packet): array
    {
        $responses = [];
        $requests = $this->readField($packet, 'requests') ?? [$packet];
        foreach (is_iterable($requests) ? $requests : [$requests] as $request) {
            $responses[] = (new ItemStackRequestExecutor($this->player, $this->inventoryManager, $request))->buildItemStackResponse();
        }
        $this->recordGameEvent('ItemStackRequest', $packet);
        return $responses;
    }

    public function handleActorEvent(mixed $packet = null): bool { return $this->recordGameEvent('ActorEvent', $packet); }
    public function handleActorPickRequest(mixed $packet = null): bool { return $this->recordGameEvent('ActorPickRequest', $packet); }
    public function handleAnimate(mixed $packet = null): bool { return $this->recordGameEvent('Animate', $packet); }
    public function handleBlockActorData(mixed $packet = null): bool { return $this->recordGameEvent('BlockActorData', $packet); }
    public function handleBlockPickRequest(mixed $packet = null): bool { return $this->recordGameEvent('BlockPickRequest', $packet); }
    public function handleBookEdit(mixed $packet = null): bool { return $this->recordGameEvent('BookEdit', $packet); }
    public function handleBossEvent(mixed $packet = null): bool { return $this->recordGameEvent('BossEvent', $packet); }
    public function handleCommandBlockUpdate(mixed $packet = null): bool { return $this->recordGameEvent('CommandBlockUpdate', $packet); }
    public function handleCommandRequest(mixed $packet = null): bool { return $this->recordGameEvent('CommandRequest', $packet); }
    public function handleContainerClose(mixed $packet = null): bool { return $this->recordGameEvent('ContainerClose', $packet); }
    public function handleEmote(mixed $packet = null): bool { return $this->recordGameEvent('Emote', $packet); }
    public function handleInteract(mixed $packet = null): bool { return $this->recordGameEvent('Interact', $packet); }
    public function handleInventoryTransaction(mixed $packet = null): bool { return $this->recordGameEvent('InventoryTransaction', $packet); }
    public function handleLabTable(mixed $packet = null): bool { return $this->recordGameEvent('LabTable', $packet); }
    public function handleLecternUpdate(mixed $packet = null): bool { return $this->recordGameEvent('LecternUpdate', $packet); }
    public function handleLevelSoundEvent(mixed $packet = null): bool { return $this->recordGameEvent('LevelSoundEvent', $packet); }
    public function handleMapInfoRequest(mixed $packet = null): bool { return $this->recordGameEvent('MapInfoRequest', $packet); }
    public function handleMobArmorEquipment(mixed $packet = null): bool { return $this->recordGameEvent('MobArmorEquipment', $packet); }
    public function handleMobEquipment(mixed $packet = null): bool { return $this->recordGameEvent('MobEquipment', $packet); }
    public function handleModalFormResponse(mixed $packet = null): bool { return $this->recordGameEvent('ModalFormResponse', $packet); }
    public function handleMovePlayer(mixed $packet = null): bool { return $this->recordGameEvent('MovePlayer', $packet); }
    public function handleNetworkStackLatency(mixed $packet = null): bool { return $this->recordGameEvent('NetworkStackLatency', $packet); }
    public function handlePlayerAction(mixed $packet = null): bool { return $this->recordGameEvent('PlayerAction', $packet); }
    public function handlePlayerAuthInput(mixed $packet = null): bool { return $this->recordGameEvent('PlayerAuthInput', $packet); }
    public function handlePlayerHotbar(mixed $packet = null): bool { return $this->recordGameEvent('PlayerHotbar', $packet); }
    public function handlePlayerSkin(mixed $packet = null): bool { return $this->recordGameEvent('PlayerSkin', $packet); }
    public function handleRequestChunkRadius(mixed $packet = null): bool { return $this->recordGameEvent('RequestChunkRadius', $packet); }
    public function handleServerSettingsRequest(mixed $packet = null): bool { return $this->recordGameEvent('ServerSettingsRequest', $packet); }
    public function handleSetActorMotion(mixed $packet = null): bool { return $this->recordGameEvent('SetActorMotion', $packet); }
    public function handleSetPlayerGameType(mixed $packet = null): bool { return $this->recordGameEvent('SetPlayerGameType', $packet); }
    public function handleShowCredits(mixed $packet = null): bool { return $this->recordGameEvent('ShowCredits', $packet); }
    public function handleSpawnExperienceOrb(mixed $packet = null): bool { return $this->recordGameEvent('SpawnExperienceOrb', $packet); }
    public function handleSubClientLogin(mixed $packet = null): bool { return $this->recordGameEvent('SubClientLogin', $packet); }

    public function __call(string $name, array $arguments): bool
    {
        if (str_starts_with($name, 'handle')) {
            return $this->recordGameEvent(substr($name, 6), $arguments[0] ?? null);
        }
        throw new \BadMethodCallException("Undefined method " . self::class . "::$name()");
    }

    /** @return list<array{name: string, packet: mixed}> */
    public function getGameEvents(): array
    {
        return $this->gameEvents;
    }

    private function recordGameEvent(string $name, mixed $packet): bool
    {
        $this->gameEvents[] = ['name' => $name, 'packet' => $packet];
        if (isset($this->callbacks[$name])) {
            return (bool) ($this->callbacks[$name])($packet, $this->player, $this->session);
        }
        return true;
    }

    private function readField(mixed $packet, string $field): mixed
    {
        if (is_array($packet) && array_key_exists($field, $packet)) {
            return $packet[$field];
        }
        if (is_object($packet)) {
            if (isset($packet->{$field})) {
                return $packet->{$field};
            }
            $method = 'get' . ucfirst($field);
            if (method_exists($packet, $method)) {
                return $packet->{$method}();
            }
        }
        return null;
    }
}
