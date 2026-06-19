<?php

declare(strict_types=1);

namespace pocketmine\player;

class OfflinePlayer implements IPlayer
{
    public function __construct(private string $name, private mixed $namedtag = null) {}

    public function getName(): string { return $this->name; }

    public function getFirstPlayed(): ?int
    {
        return $this->readTagInt(Player::TAG_FIRST_PLAYED);
    }

    public function getLastPlayed(): ?int
    {
        return $this->readTagInt(Player::TAG_LAST_PLAYED);
    }

    public function hasPlayedBefore(): bool
    {
        return $this->namedtag !== null;
    }

    private function readTagInt(string $name): ?int
    {
        if (is_array($this->namedtag) && isset($this->namedtag[$name]) && is_numeric($this->namedtag[$name])) {
            return (int) $this->namedtag[$name];
        }
        if (is_object($this->namedtag) && method_exists($this->namedtag, 'getTag')) {
            $tag = $this->namedtag->getTag($name);
            if (is_object($tag) && method_exists($tag, 'getValue')) {
                $value = $tag->getValue();
                return is_numeric($value) ? (int) $value : null;
            }
        }
        return null;
    }
}
