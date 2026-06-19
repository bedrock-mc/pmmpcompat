<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class NoteInstrumentIdMap
{
    use \pocketmine\utils\SingletonTrait;
    use CompatIdMapTrait;

    public function __construct(mixed ...$args) { $this->seedEnumCases(\pocketmine\world\sound\NoteInstrument::class); }
}
