<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\block\utils\SignText;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class BaseSign extends Block
{
    protected SignText $text;
    protected SignText $backText;
    private bool $waxed = false;
    protected ?int $editorEntityRuntimeId = null;

    public function __construct()
    {
        parent::__construct('minecraft:sign', 'Sign');
        $this->text = new SignText();
        $this->backText = new SignText();
    }

    public function asItem(): Item
    {
        return VanillaItems::AIR();
    }

    public function getFuelTime(): int
    {
        return 200;
    }

    public function getMaxStackSize(): int
    {
        return 16;
    }

    public function getSupportType(mixed ...$args): mixed
    {
        return null;
    }

    public function isSolid(): bool
    {
        return false;
    }

    public function getText(): SignText
    {
        return $this->text;
    }

    public function setText(SignText $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getFaceText(bool $frontFace): SignText
    {
        return $frontFace ? $this->text : $this->backText;
    }

    public function setFaceText(bool $frontFace, SignText $text): self
    {
        if ($frontFace) {
            $this->text = $text;
        } else {
            $this->backText = $text;
        }
        return $this;
    }

    public function isWaxed(): bool
    {
        return $this->waxed;
    }

    public function setWaxed(bool $waxed): self
    {
        $this->waxed = $waxed;
        return $this;
    }

    public function getEditorEntityRuntimeId(): ?int
    {
        return $this->editorEntityRuntimeId;
    }

    public function setEditorEntityRuntimeId(?int $editorEntityRuntimeId): self
    {
        $this->editorEntityRuntimeId = $editorEntityRuntimeId;
        return $this;
    }

    public function updateText(Player $author, SignText $text): bool
    {
        return $this->updateFaceText($author, true, $text);
    }

    public function updateFaceText(Player $author, bool $frontFace, SignText $text): bool
    {
        $size = 0;
        foreach ($text->getLines() as $line) {
            $size += strlen($line);
        }
        if ($size > 1000) {
            throw new \UnexpectedValueException($author->getName() . ' tried to write ' . $size . ' bytes of text onto a sign (bigger than max 1000)');
        }

        $oldText = $this->getFaceText($frontFace);
        $cleanText = new SignText(array_map(fn(string $line): string => TextFormat::clean($line, false), $text->getLines()), $oldText->getBaseColor(), $oldText->isGlowing());
        $event = new \pocketmine\event\block\SignChangeEvent($this, $author, $cleanText, $frontFace);
        $authorId = method_exists($author, 'getId') ? $author->getId() : null;
        if ($this->waxed || ($this->editorEntityRuntimeId !== null && $authorId !== null && $this->editorEntityRuntimeId !== $authorId)) {
            $event->cancel();
        }
        $event->call();
        if (!$event->isCancelled()) {
            $this->setFaceText($frontFace, $event->getNewText());
            $this->setEditorEntityRuntimeId(null);
            return true;
        }
        return false;
    }

    public function onInteract(mixed ...$args): bool
    {
        return true;
    }

    public function onNearbyBlockChange(): void {}

    public function onPostPlace(): void {}

    public function place(mixed ...$args): bool
    {
        return true;
    }

    public function readStateFromWorld(): self
    {
        return $this;
    }

    public function writeStateToWorld(): void {}
}
