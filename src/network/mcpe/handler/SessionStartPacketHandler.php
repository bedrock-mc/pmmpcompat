<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Handles the first client request before resource-pack negotiation.
 */
class SessionStartPacketHandler extends PacketHandler
{
    private bool $networkSettingsRequested = false;

    public function __construct(private mixed $session = null, private ?\Closure $onRequestNetworkSettings = null) {}

    public function handleRequestNetworkSettings(mixed $packet = null): bool
    {
        $this->networkSettingsRequested = true;
        if ($this->onRequestNetworkSettings !== null) {
            ($this->onRequestNetworkSettings)($packet, $this->session);
        } elseif (is_object($this->session) && method_exists($this->session, 'sendNetworkSettings')) {
            $this->session->sendNetworkSettings($packet);
        }
        return true;
    }

    public function hasRequestedNetworkSettings(): bool
    {
        return $this->networkSettingsRequested;
    }
}
