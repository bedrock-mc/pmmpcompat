<?php

declare(strict_types=1);

namespace pocketmine\lang;

final class Translatable
{
    /** @var array<int|string, string|Translatable> */
    protected array $params = [];

    /** @param array<int|string, float|int|string|Translatable> $params */
    public function __construct(protected string $text, array $params = [])
    {
        foreach ($params as $key => $param) {
            $this->params[$key] = $param instanceof self ? $param : (string) $param;
        }
    }

    public function getText(): string
    {
        return $this->text;
    }

    /** @return array<int|string, string|Translatable> */
    public function getParameters(): array
    {
        return $this->params;
    }

    public function getParameter(int|string $i): Translatable|string|null
    {
        return $this->params[$i] ?? null;
    }

    public function format(string $before, string $after): self
    {
        return new self($before . '%' . $this->text . $after, $this->params);
    }

    public function prefix(string $prefix): self
    {
        return new self($prefix . '%' . $this->text, $this->params);
    }

    public function postfix(string $postfix): self
    {
        return new self('%' . $this->text . $postfix, $this->params);
    }
}
