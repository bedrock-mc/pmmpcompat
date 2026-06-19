<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Local login phase handler.
 */
class LoginPacketHandler extends PacketHandler
{
    private mixed $lastLoginPacket = null;

    public function __construct(
        private mixed $server = null,
        private mixed $session = null,
        private ?\Closure $playerInfoConsumer = null,
        private ?\Closure $authCallback = null
    ) {}

    public function handleLogin(mixed $packet): bool
    {
        $this->lastLoginPacket = $packet;
        ($this->playerInfoConsumer ?? static fn () => null)($this->extractPlayerInfo($packet));
        ($this->authCallback ?? static fn () => null)(false, false, null, null);
        return true;
    }

    public function getLastLoginPacket(): mixed
    {
        return $this->lastLoginPacket;
    }

    /** @return array<string, mixed> */
    private function extractPlayerInfo(mixed $packet): array
    {
        return [
            'server' => $this->server,
            'session' => $this->session,
            'username' => $this->readField($packet, 'username') ?? $this->readField($packet, 'displayName'),
            'xuid' => $this->readField($packet, 'xuid'),
            'uuid' => $this->readField($packet, 'uuid'),
            'rawPacket' => $packet,
        ];
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
