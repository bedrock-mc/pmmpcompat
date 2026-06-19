<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

class Gaussian
{
    /** @var float[] */
    private array $kernel;

    public function __construct(private int $smoothSize = 2, private float $bellSize = 1.0)
    {
        $this->smoothSize = max(0, $smoothSize);
        $this->bellSize = max(0.000001, $bellSize);
        $this->kernel = $this->buildKernel();
    }

    /** @return float[] */
    public function getKernel(): array
    {
        return $this->kernel;
    }

    public function getSmoothSize(): int
    {
        return $this->smoothSize;
    }

    public function getBellSize(): float
    {
        return $this->bellSize;
    }

    /** @return float[] */
    private function buildKernel(): array
    {
        $weights = [];
        $sum = 0.0;
        for ($i = -$this->smoothSize; $i <= $this->smoothSize; ++$i) {
            $weight = exp(-($i * $i) / (2.0 * $this->bellSize * $this->bellSize));
            $weights[] = $weight;
            $sum += $weight;
        }

        if ($sum === 0.0) {
            return [1.0];
        }

        foreach ($weights as $i => $weight) {
            $weights[$i] = $weight / $sum;
        }
        return $weights;
    }
}
