<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\utils\NotCloneable;
use pocketmine\utils\NotSerializable;

/**
 * Represents a unique chunk lock token for APIs that mirror PMMP chunk locking.
 */
final class ChunkLockId
{
    use NotCloneable;
    use NotSerializable;
}
