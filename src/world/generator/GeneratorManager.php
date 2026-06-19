<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

use pocketmine\world\generator\hell\Nether;
use pocketmine\world\generator\normal\Normal;

final class GeneratorManager
{
    /** @var array<string, GeneratorManagerEntry> */
    private array $list = [];

    public function __construct()
    {
        $this->addGenerator(Flat::class, 'flat', static function(string $preset): ?InvalidGeneratorOptionsException {
            if ($preset === '') {
                return null;
            }
            try {
                FlatGeneratorOptions::parsePreset($preset);
                return null;
            } catch (InvalidGeneratorOptionsException $e) {
                return $e;
            }
        }, fast: true);
        $this->addGenerator(Normal::class, 'normal', static fn(): ?InvalidGeneratorOptionsException => null);
        $this->addAlias('normal', 'default');
        $this->addGenerator(Nether::class, 'nether', static fn(): ?InvalidGeneratorOptionsException => null);
        $this->addAlias('nether', 'hell');
    }

    public function addGenerator(string $class, string $name, \Closure $presetValidator, bool $overwrite = false, bool $fast = false): void
    {
        if (!is_a($class, Generator::class, true)) {
            throw new \InvalidArgumentException('Class ' . $class . ' does not extend ' . Generator::class);
        }
        $name = strtolower($name);
        if (!$overwrite && isset($this->list[$name])) {
            throw new \InvalidArgumentException("Alias \"$name\" is already assigned");
        }
        $this->list[$name] = new GeneratorManagerEntry($class, $presetValidator, $fast);
    }

    public function addAlias(string $name, string $alias): void
    {
        $name = strtolower($name);
        $alias = strtolower($alias);
        if (!isset($this->list[$name])) {
            throw new \InvalidArgumentException("Alias \"$name\" is not assigned");
        }
        if (isset($this->list[$alias])) {
            throw new \InvalidArgumentException("Alias \"$alias\" is already assigned");
        }
        $this->list[$alias] = $this->list[$name];
    }

    /** @return string[] */
    public function getGeneratorList(): array { return array_keys($this->list); }
    public function getGenerator(string $name): ?GeneratorManagerEntry { return $this->list[strtolower($name)] ?? null; }

    public function getGeneratorName(string $class): string
    {
        if (!is_a($class, Generator::class, true)) {
            throw new \InvalidArgumentException('Class ' . $class . ' does not extend ' . Generator::class);
        }
        foreach ($this->list as $name => $entry) {
            if ($entry->getGeneratorClass() === $class) {
                return $name;
            }
        }
        throw new \InvalidArgumentException("Generator class $class is not registered");
    }
}
