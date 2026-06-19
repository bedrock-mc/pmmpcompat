<?php

declare(strict_types=1);

namespace pocketmine\world;

use pocketmine\block\Block;
use pocketmine\math\Vector3;

class BlockTransaction
{
    /** @var array<int, array<int, array<int, Block>>> */
    private array $blocks = [];
    /** @var \Closure[] */
    private array $validators = [];

    public function __construct(private mixed $world = null)
    {
        $this->addValidator(fn(mixed $world, int $x, int $y, int $z): bool => !is_object($world) || !method_exists($world, 'isInWorld') || $world->isInWorld($x, $y, $z));
    }

    public function addBlock(Vector3 $pos, Block $state): self
    {
        return $this->addBlockAt((int) floor($pos->x), (int) floor($pos->y), (int) floor($pos->z), $state);
    }

    public function addBlockAt(int $x, int $y, int $z, Block $state): self
    {
        $this->blocks[$x][$y][$z] = $state;
        return $this;
    }

    public function fetchBlock(Vector3 $pos): Block
    {
        return $this->fetchBlockAt((int) floor($pos->x), (int) floor($pos->y), (int) floor($pos->z));
    }

    public function fetchBlockAt(int $x, int $y, int $z): Block
    {
        if (isset($this->blocks[$x][$y][$z])) {
            return $this->blocks[$x][$y][$z];
        }
        if (is_object($this->world) && method_exists($this->world, 'getBlockAt')) {
            return $this->world->getBlockAt($x, $y, $z);
        }
        throw new \OutOfBoundsException('Block not present in transaction and no readable world was supplied.');
    }

    public function apply(): bool
    {
        foreach ($this->getBlocks() as [$x, $y, $z, $_]) {
            foreach ($this->validators as $validator) {
                if (!$validator($this->world, $x, $y, $z)) {
                    return false;
                }
            }
        }
        $changed = false;
        if (is_object($this->world) && method_exists($this->world, 'setBlockAt') && method_exists($this->world, 'getBlockAt')) {
            foreach ($this->getBlocks() as [$x, $y, $z, $block]) {
                $old = $this->world->getBlockAt($x, $y, $z);
                if (!$old->isSameState($block)) {
                    $this->world->setBlockAt($x, $y, $z, $block);
                    $changed = true;
                }
            }
        }
        return $changed;
    }

    public function getBlocks(): \Generator
    {
        foreach ($this->blocks as $x => $yLine) {
            foreach ($yLine as $y => $zLine) {
                foreach ($zLine as $z => $block) {
                    yield [(int) $x, (int) $y, (int) $z, $block];
                }
            }
        }
    }

    public function addValidator(\Closure $validator): void
    {
        $this->validators[] = $validator;
    }

    public function dummyValidator(mixed $world, int $x, int $y, int $z): bool
    {
        return true;
    }
}
