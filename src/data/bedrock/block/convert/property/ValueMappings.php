<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

use pocketmine\utils\SingletonTrait;

class ValueMappings
{
    use SingletonTrait;

    public IntFromRawStateMap $cardinalDirection;
    public IntFromRawStateMap $blockFace;
    public IntFromRawStateMap $pillarAxis;
    public IntFromRawStateMap $torchFacing;
    public IntFromRawStateMap $horizontalFacing5Minus;
    public IntFromRawStateMap $horizontalFacingSWNE;
    public IntFromRawStateMap $horizontalFacingSWNEInverted;
    public IntFromRawStateMap $horizontalFacingCoral;
    public IntFromRawStateMap $horizontalFacingClassic;
    public IntFromRawStateMap $facing;
    public IntFromRawStateMap $facingEndRod;
    public IntFromRawStateMap $coralAxis;
    public IntFromRawStateMap $facingExceptDown;
    public IntFromRawStateMap $facingExceptUp;
    public IntFromRawStateMap $facingStem;

    public function __construct()
    {
        $horizontal = [2 => 'north', 3 => 'south', 4 => 'west', 5 => 'east'];
        $facing = [0 => 'down', 1 => 'up', 2 => 'north', 3 => 'south', 4 => 'west', 5 => 'east'];
        $this->cardinalDirection = IntFromRawStateMap::string([2 => 'north', 3 => 'south', 4 => 'west', 5 => 'east']);
        $this->blockFace = IntFromRawStateMap::string($facing);
        $this->pillarAxis = IntFromRawStateMap::string([0 => 'y', 1 => 'x', 2 => 'z']);
        $this->torchFacing = IntFromRawStateMap::string([0 => 'unknown', 1 => 'west', 2 => 'east', 3 => 'north', 4 => 'south', 5 => 'top']);
        $this->horizontalFacing5Minus = IntFromRawStateMap::int([2 => 3, 3 => 2, 4 => 1, 5 => 0]);
        $this->horizontalFacingSWNE = IntFromRawStateMap::int([2 => 2, 3 => 0, 4 => 1, 5 => 3]);
        $this->horizontalFacingSWNEInverted = IntFromRawStateMap::int([2 => 0, 3 => 2, 4 => 3, 5 => 1]);
        $this->horizontalFacingCoral = IntFromRawStateMap::int([2 => 0, 3 => 2, 4 => 3, 5 => 1]);
        $this->horizontalFacingClassic = IntFromRawStateMap::int($this->horizontalFacingIdentity($horizontal));
        $this->facing = IntFromRawStateMap::int($this->facingIdentity($facing));
        $this->facingEndRod = $this->facing;
        $this->coralAxis = IntFromRawStateMap::int([0 => 0, 1 => 1, 2 => 2]);
        $this->facingExceptDown = $this->facing;
        $this->facingExceptUp = $this->facing;
        $this->facingStem = $this->facing;
    }

    /** @param array<int, string> $map @return array<int, int> */
    private function horizontalFacingIdentity(array $map): array
    {
        return array_combine(array_keys($map), array_keys($map));
    }

    /** @param array<int, string> $map @return array<int, int> */
    private function facingIdentity(array $map): array
    {
        return array_combine(array_keys($map), array_keys($map));
    }
}
