<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\nbt\tag\Tag;

final class BlockStateUpgradeSchema
{
    /** @var array<string, string> */
    public array $renamedIds = [];
    /** @var array<string, array<string, Tag>> */
    public array $addedProperties = [];
    /** @var array<string, list<string>> */
    public array $removedProperties = [];
    /** @var array<string, array<string, string>> */
    public array $renamedProperties = [];
    /** @var array<string, array<string, list<BlockStateUpgradeSchemaValueRemap>>> */
    public array $remappedPropertyValues = [];
    /** @var array<string, BlockStateUpgradeSchemaFlattenInfo> */
    public array $flattenedProperties = [];
    /** @var array<string, list<BlockStateUpgradeSchemaBlockRemap>> */
    public array $remappedStates = [];

    public readonly int $versionId;

    public function __construct(
        public readonly int $maxVersionMajor,
        public readonly int $maxVersionMinor,
        public readonly int $maxVersionPatch,
        public readonly int $maxVersionRevision,
        private int $schemaId
    ) {
        $this->versionId = ($this->maxVersionMajor << 24) | ($this->maxVersionMinor << 16) | ($this->maxVersionPatch << 8) | $this->maxVersionRevision;
    }

    public function getVersionId(): int
    {
        return $this->versionId;
    }

    public function getSchemaId(): int
    {
        return $this->schemaId;
    }

    public function isEmpty(): bool
    {
        foreach ([
            $this->renamedIds,
            $this->addedProperties,
            $this->removedProperties,
            $this->renamedProperties,
            $this->remappedPropertyValues,
            $this->flattenedProperties,
            $this->remappedStates,
        ] as $list) {
            if ($list !== []) {
                return false;
            }
        }
        return true;
    }
}
