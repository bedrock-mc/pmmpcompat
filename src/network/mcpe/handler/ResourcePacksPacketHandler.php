<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

use pocketmine\network\PacketHandlingException;

/**
 * Resource-pack negotiation state machine.
 */
class ResourcePacksPacketHandler extends PacketHandler
{
    /** @var list<mixed> */
    private array $chunkRequests = [];

    private string $status = 'waiting';

    public function __construct(
        private mixed $session = null,
        private array $resourcePackStack = [],
        private array $encryptionKeys = [],
        private bool $mustAccept = false,
        private ?\Closure $completionCallback = null
    ) {}

    public function setUp(): void
    {
        parent::setUp();
        $this->status = 'info-sent';
    }

    public function handleResourcePackChunkRequest(mixed $packet): bool
    {
        if ($this->status === 'complete') {
            throw new PacketHandlingException('Cannot request resource pack chunks after completion');
        }
        $this->chunkRequests[] = $packet;
        return true;
    }

    public function handleResourcePackClientResponse(mixed $packet): bool
    {
        $status = $this->readStatus($packet);
        $this->status = match ($status) {
            'refused', 1 => 'refused',
            'send_packs', 'send-packs', 2 => 'metadata-requested',
            'have_all_packs', 'have-all-packs', 3 => 'stack-sent',
            'completed', 4 => 'complete',
            default => (string) $status,
        };

        if ($this->status === 'refused' && $this->mustAccept) {
            throw new PacketHandlingException('Client refused required resource packs');
        }
        if ($this->status === 'complete') {
            ($this->completionCallback ?? static fn () => null)($packet, $this->session);
        }
        return true;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /** @return list<mixed> */
    public function getChunkRequests(): array
    {
        return $this->chunkRequests;
    }

    private function readStatus(mixed $packet): mixed
    {
        if (is_array($packet)) {
            return $packet['status'] ?? null;
        }
        if (is_object($packet)) {
            return $packet->status ?? (method_exists($packet, 'getStatus') ? $packet->getStatus() : null);
        }
        return $packet;
    }
}
