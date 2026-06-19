<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

interface EntityEventBroadcaster
{
    public function syncAttributes(array $recipients, mixed $entity, array $attributes): void;
    public function syncActorData(array $recipients, mixed $entity, array $properties): void;
    public function onEntityEffectAdded(array $recipients, mixed $entity, mixed $effect, bool $replacesOldEffect): void;
    public function onEntityEffectRemoved(array $recipients, mixed $entity, mixed $effect): void;
    public function onEntityRemoved(array $recipients, mixed $entity): void;
    public function onMobMainHandItemChange(array $recipients, mixed $mob): void;
    public function onMobOffHandItemChange(array $recipients, mixed $mob): void;
    public function onMobArmorChange(array $recipients, mixed $mob): void;
    public function onPickUpItem(array $recipients, mixed $collector, mixed $pickedUp): void;
    public function onEmote(array $recipients, mixed $from, string $emoteId): void;
}
