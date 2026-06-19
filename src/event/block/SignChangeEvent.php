<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\BaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class SignChangeEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    private SignText $oldText;

    public function __construct(
        private BaseSign $sign,
        private Player $player,
        private SignText $text,
        private bool $frontFace = true,
    ) {
        $this->oldText = $this->sign->getFaceText($this->frontFace);
        parent::__construct($sign);
    }

    public function getSign(): BaseSign
    {
        return $this->sign;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getOldText(): SignText
    {
        return $this->oldText;
    }

    public function getNewText(): SignText
    {
        return $this->text;
    }

    public function setNewText(SignText $text): void
    {
        $this->text = $text;
    }

    public function isFrontFace(): bool
    {
        return $this->frontFace;
    }
}
