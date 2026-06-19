<?php

declare(strict_types=1);

namespace pocketmine\item;

class WritableBookBase extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:writablebookbase', 'WritableBookBase'); }
    public const TAG_PAGES = 'pages';
    public const TAG_PAGE_PHOTONAME = 'photoname';
    public const TAG_PAGE_TEXT = 'text';

    /** @var string[] */
    private array $pages = [];

    public function getMaxStackSize(): int { return 1; }
    public function getPages(): array { return $this->pages; }
    public function setPages(array $pages): self
    {
        $this->pages = array_values(array_map('strval', $pages));
        return $this;
    }
    public function pageExists(int $pageId): bool { return isset($this->pages[$pageId]); }
    public function getPageText(int $pageId): string { return $this->pages[$pageId] ?? ''; }
    public function setPageText(int $pageId, string $pageText): self
    {
        if ($pageId >= 0) {
            $this->pages[$pageId] = $pageText;
            ksort($this->pages);
            $this->pages = array_values($this->pages);
        }
        return $this;
    }
    public function addPage(string $pageText): self
    {
        $this->pages[] = $pageText;
        return $this;
    }
    public function insertPage(int $pageId, string $pageText): self
    {
        array_splice($this->pages, max(0, $pageId), 0, [$pageText]);
        return $this;
    }
    public function deletePage(int $pageId): self
    {
        if (isset($this->pages[$pageId])) {
            array_splice($this->pages, $pageId, 1);
        }
        return $this;
    }
    public function swapPages(int $pageId1, int $pageId2): self
    {
        if (isset($this->pages[$pageId1], $this->pages[$pageId2])) {
            [$this->pages[$pageId1], $this->pages[$pageId2]] = [$this->pages[$pageId2], $this->pages[$pageId1]];
        }
        return $this;
    }
}
