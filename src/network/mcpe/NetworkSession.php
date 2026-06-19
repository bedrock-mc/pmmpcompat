<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\network\mcpe\compression\Compressor;
use pocketmine\network\mcpe\compression\ZlibCompressor;
use pocketmine\network\mcpe\convert\TypeConverter;

/**
 * PHP-local PMMP network session facade.
 *
 * Dragonfly owns the real connection. This object keeps plugin-visible session
 * state, packet buffers and callbacks so PMMP plugins can call the expected API.
 */
class NetworkSession
{
    private bool $connected = true;
    private mixed $handler = null;
    private mixed $player = null;
    private mixed $playerInfo = null;
    private string $displayName = '';
    private string $disconnectReason = '';
    private int $ping = 0;
    private InventoryManager $inventoryManager;
    private PacketBroadcaster $broadcaster;
    private EntityEventBroadcaster $entityEventBroadcaster;
    private TypeConverter $typeConverter;
    private Compressor $compressor;
    /** @var list<array{packet: mixed, receipt: ?int}> */
    private array $sentPackets = [];
    /** @var list<string> */
    private array $sendBuffer = [];
    /** @var array<string, true> */
    private array $usedChunks = [];
    /** @var list<array{name: string, args: array<int, mixed>}> */
    private array $events = [];

    public function __construct(
        private string $ip = '127.0.0.1',
        private int $port = 19132,
        mixed $playerInfo = null,
        ?Compressor $compressor = null,
        ?TypeConverter $typeConverter = null,
    ) {
        $this->playerInfo = $playerInfo;
        $this->compressor = $compressor ?? ZlibCompressor::getInstance();
        $this->typeConverter = $typeConverter ?? TypeConverter::getInstance();
        $this->inventoryManager = new InventoryManager($this);
        $this->broadcaster = new StandardPacketBroadcaster();
        $this->entityEventBroadcaster = new StandardEntityEventBroadcaster($this->broadcaster);
    }

    public static function encodePacketTimed(mixed $packet, ?\Closure $onTimings = null): string
    {
        $start = hrtime(true);
        $encoded = self::encodePacket($packet);
        if ($onTimings !== null) {
            $onTimings($packet, (hrtime(true) - $start) / 1_000_000);
        }
        return $encoded;
    }

    public function addToSendBuffer(string $payload): void { $this->sendBuffer[] = $payload; }
    public function queueCompressed(string $payload, bool $immediate = false, ?int $receiptId = null): void { $this->sendDataPacket($payload, $immediate, $receiptId); }
    public function sendDataPacket(mixed $packet, bool $immediate = false, ?int $receiptId = null): bool
    {
        $this->sentPackets[] = ['packet' => $packet, 'receipt' => $receiptId];
        $this->sendBuffer[] = self::encodePacket($packet);
        return $this->connected;
    }

    public function sendDataPacketWithReceipt(mixed $packet, bool $immediate = false): ?int
    {
        $receiptId = count($this->sentPackets) + 1;
        $this->sendDataPacket($packet, $immediate, $receiptId);
        return $receiptId;
    }

    public function handleDataPacket(mixed $packet): mixed
    {
        if ($this->handler !== null && method_exists($this->handler, 'handlePacket')) {
            return $this->handler->handlePacket($packet);
        }
        return false;
    }

    public function handleEncoded(string $payload): bool
    {
        $this->record(__FUNCTION__, [$payload]);
        return $this->connected;
    }

    public function handleAckReceipt(int $receiptId): void { $this->record(__FUNCTION__, [$receiptId]); }
    public function disconnect(string $reason = 'disconnected'): void { $this->connected = false; $this->disconnectReason = $reason; }
    public function disconnectWithError(string $reason): void { $this->disconnect($reason); }
    public function disconnectIncompatibleProtocol(int|string $protocol): void { $this->disconnect('Incompatible protocol ' . $protocol); }
    public function onClientDisconnect(string $reason = 'client disconnect'): void { $this->disconnect($reason); }

    public function isConnected(): bool { return $this->connected; }
    public function getIp(): string { return $this->ip; }
    public function getPort(): int { return $this->port; }
    public function getPing(): int { return $this->ping; }
    public function updatePing(int $ping): void { $this->ping = max(0, $ping); }
    public function getDisplayName(): string { return $this->displayName; }
    public function getPlayer(): mixed { return $this->player; }
    public function getPlayerInfo(): mixed { return $this->playerInfo; }
    public function getHandler(): mixed { return $this->handler; }
    public function setHandler(mixed $handler): void { $this->handler = $handler; }
    public function getInvManager(): InventoryManager { return $this->inventoryManager; }
    public function getBroadcaster(): PacketBroadcaster { return $this->broadcaster; }
    public function getEntityEventBroadcaster(): EntityEventBroadcaster { return $this->entityEventBroadcaster; }
    public function getCompressor(): Compressor { return $this->compressor; }
    public function getTypeConverter(): TypeConverter { return $this->typeConverter; }
    public function getLogger(): mixed { return null; }

    public function onPlayerAdded(mixed $player): void { $this->player = $player; $this->displayName = $this->readName($player); $this->record(__FUNCTION__, [$player]); }
    public function onPlayerRemoved(mixed $player): void { $this->record(__FUNCTION__, [$player]); }
    public function onPlayerDestroyed(mixed $player): void { $this->record(__FUNCTION__, [$player]); }
    public function onEnterWorld(): void { $this->record(__FUNCTION__, []); }
    public function notifyTerrainReady(): void { $this->record(__FUNCTION__, []); }
    public function tick(): void { $this->record(__FUNCTION__, []); }

    public function startUsingChunk(int $chunkX, int $chunkZ): void { $this->usedChunks["$chunkX:$chunkZ"] = true; }
    public function stopUsingChunk(int $chunkX, int $chunkZ): void { unset($this->usedChunks["$chunkX:$chunkZ"]); }

    public function transfer(string $address, int $port = 19132, string $message = ''): void { $this->record(__FUNCTION__, [$address, $port, $message]); }
    public function prepareClientTranslatableMessage(string $message, array $params = []): string { return vsprintf(str_replace('%', '%%', $message), $params); }

    public function syncAbilities(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncAdventureSettings(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncAvailableCommands(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncGameMode(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncMovement(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncPlayerList(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncPlayerSpawnPoint(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncViewAreaCenterPoint(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncViewAreaRadius(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncWorldDifficulty(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncWorldSpawnPoint(): void { $this->record(__FUNCTION__, func_get_args()); }
    public function syncWorldTime(): void { $this->record(__FUNCTION__, func_get_args()); }

    public function onActionBar(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onChatMessage(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onClearTitle(): void { $this->record(__FUNCTION__, []); }
    public function onCloseAllForms(): void { $this->record(__FUNCTION__, []); }
    public function onFormSent(mixed $form, int $formId): void { $this->record(__FUNCTION__, [$form, $formId]); }
    public function onItemCooldownChanged(mixed ...$args): void { $this->record(__FUNCTION__, $args); }
    public function onJukeboxPopup(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onOpenSignEditor(mixed ...$args): void { $this->record(__FUNCTION__, $args); }
    public function onPopup(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onResetTitleOptions(): void { $this->record(__FUNCTION__, []); }
    public function onServerDeath(): void { $this->record(__FUNCTION__, []); }
    public function onServerRespawn(): void { $this->record(__FUNCTION__, []); }
    public function onSubTitle(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onTip(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onTitle(string $message): void { $this->record(__FUNCTION__, [$message]); }
    public function onTitleDuration(int $fadeIn, int $stay, int $fadeOut): void { $this->record(__FUNCTION__, [$fadeIn, $stay, $fadeOut]); }
    public function onToastNotification(string $title, string $body): void { $this->record(__FUNCTION__, [$title, $body]); }

    /** @return list<array{packet: mixed, receipt: ?int}> */
    public function getSentPackets(): array { return $this->sentPackets; }
    /** @return list<string> */
    public function getSendBuffer(): array { return $this->sendBuffer; }
    /** @return list<array{name: string, args: array<int, mixed>}> */
    public function getRecordedEvents(): array { return $this->events; }
    public function getDisconnectReason(): string { return $this->disconnectReason; }

    private static function encodePacket(mixed $packet): string
    {
        if (is_string($packet)) {
            return $packet;
        }
        if (is_object($packet) && method_exists($packet, 'encode')) {
            return (string) $packet->encode();
        }
        return json_encode($packet, JSON_THROW_ON_ERROR | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /** @param array<int, mixed> $args */
    private function record(string $name, array $args): void { $this->events[] = ['name' => $name, 'args' => $args]; }

    private function readName(mixed $player): string
    {
        if (is_object($player) && method_exists($player, 'getName')) {
            return (string) $player->getName();
        }
        return is_object($player) && isset($player->name) ? (string) $player->name : '';
    }
}
