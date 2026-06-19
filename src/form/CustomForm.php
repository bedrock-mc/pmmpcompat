<?php

declare(strict_types=1);

namespace pocketmine\form;

use pocketmine\player\Player;

class CustomForm implements Form
{
    /** @var list<array<string, mixed>> */
    private array $content = [];

    public function __construct(private string $title = '', private ?\Closure $callback = null) {}

    public function addLabel(string $text): self { $this->content[] = ['type' => 'label', 'text' => $text]; return $this; }
    public function addInput(string $text, string $placeholder = '', ?string $default = null): self
    {
        $this->content[] = ['type' => 'input', 'text' => $text, 'placeholder' => $placeholder, 'default' => $default ?? ''];
        return $this;
    }
    public function addToggle(string $text, bool $default = false): self
    {
        $this->content[] = ['type' => 'toggle', 'text' => $text, 'default' => $default];
        return $this;
    }
    /** @param string[] $options */
    public function addDropdown(string $text, array $options, int $default = 0): self
    {
        $this->content[] = ['type' => 'dropdown', 'text' => $text, 'options' => array_values($options), 'default' => $default];
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
        return ['type' => 'custom_form', 'title' => $this->title, 'content' => $this->content];
    }
}
