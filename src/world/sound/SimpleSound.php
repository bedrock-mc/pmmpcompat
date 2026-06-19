<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

abstract class SimpleSound implements Sound
{
    /** @var list<mixed> */
    private array $constructorArgs;

    public function __construct(mixed ...$args)
    {
        $this->constructorArgs = $args;
    }

    public function encode(mixed ...$args): array
    {
        return [
            'kind' => 'sound',
            'class' => static::class,
            'name' => $this->shortName(),
            'constructorArgs' => $this->constructorArgs,
            'encodeArgs' => $args,
        ];
    }

    /** @return list<mixed> */
    protected function constructorArgs(): array
    {
        return $this->constructorArgs;
    }

    protected function constructorArg(int $index, mixed $default = null): mixed
    {
        return $this->constructorArgs[$index] ?? $default;
    }

    protected function shortName(): string
    {
        $class = static::class;
        return substr($class, strrpos($class, '\\') + 1);
    }
}
