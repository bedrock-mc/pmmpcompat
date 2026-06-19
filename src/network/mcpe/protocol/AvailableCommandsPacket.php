<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

class AvailableCommandsPacket extends DataPacket implements ClientboundPacket
{
    public const NETWORK_ID = 0x4c;
    public const ARG_FLAG_VALID = 0x100000;
    public const ARG_FLAG_ENUM = 0x200000;
    public const ARG_TYPE_INT = 1;
    public const ARG_TYPE_FLOAT = 3;
    public const ARG_TYPE_STRING = 4;
    public const ARG_TYPE_TARGET = 5;
    public const ARG_TYPE_POSITION = 6;
    public const ARG_TYPE_RAWTEXT = 7;

    /** @var array<string, object> */
    public array $commandData = [];
    /** @var array<int|string, object> */
    public array $softEnums = [];
}
