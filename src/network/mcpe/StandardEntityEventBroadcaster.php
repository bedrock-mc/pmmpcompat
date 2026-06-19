<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

class StandardEntityEventBroadcaster implements EntityEventBroadcaster
{
    /** @var list<array{name: string, recipients: array<int, mixed>, args: array<int, mixed>}> */
    private array $events = [];

    public function __construct(private ?PacketBroadcaster $packetBroadcaster = null) {}

    public function syncAttributes(array $recipients, mixed $entity, array $attributes): void { $this->event(__FUNCTION__, $recipients, [$entity, $attributes]); }
    public function syncActorData(array $recipients, mixed $entity, array $properties): void { $this->event(__FUNCTION__, $recipients, [$entity, $properties]); }
    public function onEntityEffectAdded(array $recipients, mixed $entity, mixed $effect, bool $replacesOldEffect): void { $this->event(__FUNCTION__, $recipients, [$entity, $effect, $replacesOldEffect]); }
    public function onEntityEffectRemoved(array $recipients, mixed $entity, mixed $effect): void { $this->event(__FUNCTION__, $recipients, [$entity, $effect]); }
    public function onEntityRemoved(array $recipients, mixed $entity): void { $this->event(__FUNCTION__, $recipients, [$entity]); }
    public function onMobMainHandItemChange(array $recipients, mixed $mob): void { $this->event(__FUNCTION__, $recipients, [$mob]); }
    public function onMobOffHandItemChange(array $recipients, mixed $mob): void { $this->event(__FUNCTION__, $recipients, [$mob]); }
    public function onMobArmorChange(array $recipients, mixed $mob): void { $this->event(__FUNCTION__, $recipients, [$mob]); }
    public function onPickUpItem(array $recipients, mixed $collector, mixed $pickedUp): void { $this->event(__FUNCTION__, $recipients, [$collector, $pickedUp]); }
    public function onEmote(array $recipients, mixed $from, string $emoteId): void { $this->event(__FUNCTION__, $recipients, [$from, $emoteId]); }

    /** @return list<array{name: string, recipients: array<int, mixed>, args: array<int, mixed>}> */
    public function getEvents(): array { return $this->events; }

    /** @param array<int, mixed> $recipients @param array<int, mixed> $args */
    private function event(string $name, array $recipients, array $args): void
    {
        $this->events[] = ['name' => $name, 'recipients' => $recipients, 'args' => $args];
        $this->packetBroadcaster?->broadcastPackets($recipients, [['entityEvent' => $name, 'args' => $args]]);
    }
}
