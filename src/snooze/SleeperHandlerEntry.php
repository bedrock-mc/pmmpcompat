<?php

declare(strict_types=1);

namespace pocketmine\snooze;

final class SleeperHandlerEntry extends \pmmp\thread\ThreadSafe
{
    public function __construct(private int $handlerId, private SleeperNotifierState $state, private int $id) {}

    public function getNotifierId(): int
    {
        return $this->id;
    }

    public function createNotifier(): SleeperNotifier
    {
        return new SleeperNotifier($this->state, $this->id);
    }

    public function remove(): void
    {
        SleeperHandler::removeRegisteredNotifier($this->handlerId, $this->id);
    }
}
