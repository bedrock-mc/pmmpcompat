<?php

declare(strict_types=1);

namespace pocketmine\permission;

class BanList
{
    /** @var array<string, BanEntry> */
    private array $list = [];
    private bool $enabled = true;

    public function __construct(private string $file)
    {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $flag): void
    {
        $this->enabled = $flag;
    }

    public function getEntry(string $name): ?BanEntry
    {
        $this->removeExpired();
        return $this->list[strtolower($name)] ?? null;
    }

    public function getEntries(): array
    {
        $this->removeExpired();
        return $this->list;
    }

    public function isBanned(string $name): bool
    {
        if (!$this->enabled) {
            return false;
        }
        $this->removeExpired();
        return isset($this->list[strtolower($name)]);
    }

    public function add(BanEntry $entry): void
    {
        $this->list[$entry->getName()] = $entry;
        $this->save();
    }

    public function addBan(string $target, ?string $reason = null, ?\DateTime $expires = null, ?string $source = null): BanEntry
    {
        $entry = new BanEntry($target);
        if ($source !== null) {
            $entry->setSource($source);
        }
        $entry->setExpires($expires);
        if ($reason !== null) {
            $entry->setReason($reason);
        }
        $this->add($entry);
        return $entry;
    }

    public function remove(string $name): void
    {
        unset($this->list[strtolower($name)]);
        $this->save();
    }

    public function removeExpired(): void
    {
        foreach ($this->list as $name => $entry) {
            if ($entry->hasExpired()) {
                unset($this->list[$name]);
            }
        }
    }

    public function load(): void
    {
        $this->list = [];
        if (!is_file($this->file)) {
            return;
        }
        foreach (file($this->file, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = trim((string) $line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $entry = BanEntry::fromString($line);
            if ($entry !== null) {
                $this->list[$entry->getName()] = $entry;
            }
        }
    }

    public function save(bool $writeHeader = true): void
    {
        $this->removeExpired();
        $lines = [];
        if ($writeHeader) {
            $lines[] = '# victim name | ban date | banned by | banned until | reason';
            $lines[] = '';
        }
        foreach ($this->list as $entry) {
            $lines[] = $entry->getString();
        }
        file_put_contents($this->file, implode(PHP_EOL, $lines) . PHP_EOL);
    }
}
