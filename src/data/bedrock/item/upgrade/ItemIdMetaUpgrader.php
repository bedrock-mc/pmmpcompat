<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item\upgrade;

final class ItemIdMetaUpgrader
{
    /** @var array<int, ItemIdMetaUpgradeSchema> */
    private array $idMetaUpgradeSchemas = [];

    /** @param list<ItemIdMetaUpgradeSchema> $idMetaUpgradeSchemas */
    public function __construct(array $idMetaUpgradeSchemas = [])
    {
        foreach ($idMetaUpgradeSchemas as $schema) {
            $this->addSchema($schema);
        }
    }

    public function addSchema(ItemIdMetaUpgradeSchema $schema): void
    {
        if (isset($this->idMetaUpgradeSchemas[$schema->getSchemaId()])) {
            throw new \InvalidArgumentException('Already have a schema with priority ' . $schema->getSchemaId());
        }
        $this->idMetaUpgradeSchemas[$schema->getSchemaId()] = $schema;
        ksort($this->idMetaUpgradeSchemas, SORT_NUMERIC);
    }

    /** @return array<int, ItemIdMetaUpgradeSchema> */
    public function getSchemas(): array
    {
        return $this->idMetaUpgradeSchemas;
    }

    /** @return array{0: string, 1: int} */
    public function upgrade(string $id, int $meta): array
    {
        $newId = $id;
        $newMeta = $meta;
        foreach ($this->idMetaUpgradeSchemas as $schema) {
            if (($remappedMetaId = $schema->remapMeta($newId, $newMeta)) !== null) {
                $newId = $remappedMetaId;
                $newMeta = 0;
            } elseif (($renamedId = $schema->renameId($newId)) !== null) {
                $newId = $renamedId;
            }
        }
        return [$newId, $newMeta];
    }
}
