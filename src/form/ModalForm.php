<?php

declare(strict_types=1);

namespace pocketmine\form;

use pocketmine\player\Player;

class ModalForm implements Form
{
    public function __construct(
        private string $title = '',
        private string $content = '',
        private string $button1 = 'Yes',
        private string $button2 = 'No',
        private ?\Closure $callback = null,
    ) {}

    public function handleResponse(Player $player, mixed $data): void
    {
        if ($this->callback !== null) {
            ($this->callback)($player, (bool) $data);
        }
    }

    public function jsonSerialize(): array
    {
        return ['type' => 'modal', 'title' => $this->title, 'content' => $this->content, 'button1' => $this->button1, 'button2' => $this->button2];
    }
}
