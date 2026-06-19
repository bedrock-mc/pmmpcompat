<?php

declare(strict_types=1);

namespace pocketmine\player;

final class XboxLivePlayerInfo extends PlayerInfo
{
    /** @param array<string, mixed> $extraData */
    public function __construct(private string $xuid, string $username, mixed $uuid, mixed $skin, string $locale, array $extraData = [])
    {
        parent::__construct($username, $uuid, $skin, $locale, $extraData);
    }

    public function getXuid(): string { return $this->xuid; }

    public function withoutXboxData(): PlayerInfo
    {
        return new PlayerInfo($this->getUsername(), $this->getUuid(), $this->getSkin(), $this->getLocale(), $this->getExtraData());
    }
}
