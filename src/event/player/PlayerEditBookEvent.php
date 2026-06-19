<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\WritableBookBase;
use pocketmine\player\Player;

class PlayerEditBookEvent extends PlayerEvent implements Cancellable
{
    public const ACTION_REPLACE_PAGE = 0;
    public const ACTION_ADD_PAGE = 1;
    public const ACTION_DELETE_PAGE = 2;
    public const ACTION_SWAP_PAGES = 3;
    public const ACTION_SIGN_BOOK = 4;

    use CancellableTrait;

    public function __construct(
        Player $player,
        private WritableBookBase $oldBook,
        private WritableBookBase $newBook,
        private int $action,
        private array $modifiedPages,
    ) {
        parent::__construct($player);
    }

    public function getAction(): int { return $this->action; }
    public function getOldBook(): WritableBookBase { return $this->oldBook; }
    public function getNewBook(): WritableBookBase { return $this->newBook; }
    public function setNewBook(WritableBookBase $book): void { $this->newBook = $book; }
    public function getModifiedPages(): array { return $this->modifiedPages; }
}
