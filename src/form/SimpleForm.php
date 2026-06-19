<?php

declare(strict_types=1);

namespace pocketmine\form;

use pocketmine\player\Player;

class SimpleForm implements Form
{
    /** @var list<array<string, mixed>> */
    private array $buttons = [];

    public function __construct(private string $title = '', private string $content = '', private ?\Closure $callback = null) {}

    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function addButton(string $text, int $imageType = -1, string $imagePath = ''): self
    {
        $button = ['text' => $text];
        if ($imageType >= 0 && $imagePath !== '') {
            $button['image'] = ['type' => $imageType === 0 ? 'path' : 'url', 'data' => $imagePath];
        }
        $this->buttons[] = $button;
        return $this;
    }

    public function handleResponse(Player $player, mixed $data): void
    {
        if ($this->callback !== null) {
            ($this->callback)($player, $data);
        }
    }

    public function jsonSerialize(): array
    {
        return ['type' => 'form', 'title' => $this->title, 'content' => $this->content, 'buttons' => $this->buttons];
    }
}
