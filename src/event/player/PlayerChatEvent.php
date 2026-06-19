<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\command\CommandSender;
use pocketmine\player\chat\ChatFormatter;
use pocketmine\player\chat\LegacyRawChatFormatter;
use pocketmine\player\Player;

class PlayerChatEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var CommandSender[] */
    private array $recipients;
    private ChatFormatter $formatter;

    /** @param CommandSender[] $recipients */
    public function __construct(Player $player, private string $message, array $recipients = [], ?ChatFormatter $formatter = null)
    {
        parent::__construct($player);
        $this->recipients = $recipients;
        $this->formatter = $formatter ?? new LegacyRawChatFormatter('<{%0}> {%1}');
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getFormatter(): ChatFormatter
    {
        return $this->formatter;
    }

    public function setFormatter(ChatFormatter $formatter): void
    {
        $this->formatter = $formatter;
    }

    /** @return CommandSender[] */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /** @param CommandSender[] $recipients */
    public function setRecipients(array $recipients): void
    {
        foreach ($recipients as $recipient) {
            if (!$recipient instanceof CommandSender) {
                throw new \InvalidArgumentException('Recipients must implement CommandSender.');
            }
        }
        $this->recipients = $recipients;
    }
}
