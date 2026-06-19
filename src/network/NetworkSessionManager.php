<?php

declare(strict_types=1);

namespace pocketmine\network;

use function count;
use function method_exists;
use function spl_object_id;

class NetworkSessionManager
{
    /** @var array<int, object> */
    private array $sessions = [];
    /** @var array<int, object> */
    private array $pendingLoginSessions = [];

    public function add(object $session): void
    {
        $idx = spl_object_id($session);
        $this->sessions[$idx] = $session;
        $this->pendingLoginSessions[$idx] = $session;
    }

    public function markLoginReceived(object $session): void
    {
        unset($this->pendingLoginSessions[spl_object_id($session)]);
    }

    public function remove(object $session): void
    {
        $idx = spl_object_id($session);
        unset($this->sessions[$idx], $this->pendingLoginSessions[$idx]);
    }

    public function getSessionCount(): int
    {
        return count($this->sessions);
    }

    public function getValidSessionCount(): int
    {
        return count($this->sessions) - count($this->pendingLoginSessions);
    }

    /** @return array<int, object> */
    public function getSessions(): array
    {
        return $this->sessions;
    }

    public function tick(): void
    {
        foreach ($this->sessions as $idx => $session) {
            if (method_exists($session, 'tick')) {
                $session->tick();
            }
            if (method_exists($session, 'isConnected') && !$session->isConnected()) {
                unset($this->sessions[$idx], $this->pendingLoginSessions[$idx]);
            }
        }
    }

    public function close(mixed $reason = '', mixed $disconnectScreenMessage = null): void
    {
        foreach ($this->sessions as $session) {
            if (method_exists($session, 'disconnect')) {
                $session->disconnect($reason, $disconnectScreenMessage);
            }
        }
        $this->sessions = [];
        $this->pendingLoginSessions = [];
    }
}
