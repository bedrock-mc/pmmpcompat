<?php

declare(strict_types=1);

namespace pocketmine\network\upnp;

final class UPnP
{
    /** @throws UPnPException */
    public static function getServiceUrl(): string
    {
        throw new UPnPException('UPnP discovery is not provided by pmmpcompat');
    }

    public static function portForward(string $serviceURL, string $internalIP, int $internalPort, int $externalPort): void
    {
    }

    public static function removePortForward(string $serviceURL, int $externalPort): void
    {
    }
}
