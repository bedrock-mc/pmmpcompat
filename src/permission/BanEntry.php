<?php

declare(strict_types=1);

namespace pocketmine\permission;

class BanEntry
{
    public static string $format = 'Y-m-d H:i:s O';

    private string $name;
    private \DateTime $creationDate;
    private string $source = '(Unknown)';
    private ?\DateTime $expirationDate = null;
    private string $reason = 'Banned by an operator.';

    public function __construct(string $name)
    {
        $this->name = strtolower($name);
        $this->creationDate = new \DateTime();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreated(): \DateTime
    {
        return $this->creationDate;
    }

    public function setCreated(\DateTime $date): void
    {
        $this->creationDate = $date;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getExpires(): ?\DateTime
    {
        return $this->expirationDate;
    }

    public function setExpires(?\DateTime $date): void
    {
        $this->expirationDate = $date;
    }

    public function hasExpired(): bool
    {
        return $this->expirationDate !== null && $this->expirationDate < new \DateTime();
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getString(): string
    {
        return implode('|', [
            $this->getName(),
            $this->getCreated()->format(self::$format),
            $this->getSource(),
            $this->getExpires()?->format(self::$format) ?? 'Forever',
            $this->getReason(),
        ]);
    }

    public static function fromString(string $str): ?self
    {
        if (strlen(trim($str)) < 2) {
            return null;
        }
        $parts = explode('|', trim($str), 6);
        $entry = new self(trim(array_shift($parts)));
        if ($parts !== []) {
            $entry->setCreated(self::parseDate((string) array_shift($parts)));
        }
        if ($parts !== []) {
            $entry->setSource(trim((string) array_shift($parts)));
        }
        if ($parts !== []) {
            $expire = trim((string) array_shift($parts));
            if ($expire !== '' && strtolower($expire) !== 'forever') {
                $entry->setExpires(self::parseDate($expire));
            }
        }
        if ($parts !== []) {
            $entry->setReason(trim((string) array_shift($parts)));
        }
        return $entry;
    }

    private static function parseDate(string $date): \DateTime
    {
        $parsed = \DateTime::createFromFormat(self::$format, $date);
        if (!$parsed instanceof \DateTime) {
            throw new \RuntimeException('Corrupted date/time: ' . $date);
        }
        return $parsed;
    }
}
