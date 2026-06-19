<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Mutable MCPE handler base compatible with PocketMine's packet-handler shape.
 *
 * These handlers are intentionally local facades. They let PHP plugins exercise
 * PocketMine-style handler state transitions while Dragonfly remains the owner
 * of live network, world and entity state.
 */
class PacketHandler
{
    /** @var list<array{name: string, packet: mixed, result: mixed}> */
    private array $handledPackets = [];

    private bool $setUp = false;

    public function setUp(): void
    {
        $this->setUp = true;
    }

    public function isSetUp(): bool
    {
        return $this->setUp;
    }

    /**
     * Dispatches a packet object to handleFoo() when a PMMP-style method exists.
     */
    public function handlePacket(mixed $packet): mixed
    {
        $shortName = $this->packetShortName($packet);
        $method = 'handle' . $shortName;
        if (method_exists($this, $method)) {
            /** @var mixed $result */
            $result = $this->{$method}($packet);
            $this->recordHandled($method, $packet, $result);
            return $result;
        }

        $this->recordHandled('unhandled:' . $shortName, $packet, false);
        return false;
    }

    /** @return list<array{name: string, packet: mixed, result: mixed}> */
    public function getHandledPackets(): array
    {
        return $this->handledPackets;
    }

    protected function recordHandled(string $name, mixed $packet, mixed $result): void
    {
        $this->handledPackets[] = [
            'name' => $name,
            'packet' => $packet,
            'result' => $result,
        ];
    }

    private function packetShortName(mixed $packet): string
    {
        if (is_object($packet)) {
            $class = $packet::class;
            $separator = strrpos($class, '\\');
            $short = $separator === false ? $class : substr($class, $separator + 1);
            return str_ends_with($short, 'Packet') ? substr($short, 0, -6) : $short;
        }

        return ucfirst(get_debug_type($packet));
    }
}
