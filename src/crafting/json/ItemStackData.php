<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class ItemStackData implements \JsonSerializable
{
    public int $count = 1;
    public string $block_states = '';
    public int $meta = 0;
    public string $nbt = '';
    /** @var string[] */
    public array $can_place_on = [];
    /** @var string[] */
    public array $can_destroy = [];

    public function __construct(public string $name) {}

    public function jsonSerialize(): array|string
    {
        $result = (array) $this;
        return count($result) === 7 && $this->count === 1 && $this->block_states === '' && $this->meta === 0 && $this->nbt === '' && $this->can_place_on === [] && $this->can_destroy === [] ? $this->name : $result;
    }
}
