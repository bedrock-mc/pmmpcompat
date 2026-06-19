<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Flat;
use pocketmine\world\generator\FlatGeneratorOptions;
use pocketmine\world\generator\Gaussian;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\PopulationTask;
use pocketmine\world\generator\biome\BiomeSelector;
use pocketmine\world\generator\executor\AsyncGeneratorRegisterTask;
use pocketmine\world\generator\executor\AsyncGeneratorUnregisterTask;
use pocketmine\world\generator\executor\GeneratorExecutorSetupParameters;
use pocketmine\world\generator\executor\SyncGeneratorExecutor;
use pocketmine\world\generator\executor\ThreadLocalGeneratorContext;
use pocketmine\world\generator\noise\Noise;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\object\Ore;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\object\TreeFactory;
use pocketmine\world\generator\object\TreeType;
use pocketmine\world\generator\populator\GroundCover;
use pocketmine\world\generator\populator\Ore as OrePopulator;
use pocketmine\world\generator\populator\TallGrass as TallGrassPopulator;
use pocketmine\world\utils\SubChunkExplorer;
use pocketmine\world\utils\SubChunkExplorerStatus;

final class SmokeChunkManager implements ChunkManager
{
    /** @var array<string, Block> */
    private array $blocks = [];
    /** @var array<string, \pocketmine\world\format\Chunk> */
    private array $chunks = [];

    public function getBlockAt(int $x, int $y, int $z): Block
    {
        return $this->blocks[$x . ':' . $y . ':' . $z] ?? VanillaBlocks::AIR();
    }

    public function setBlockAt(int $x, int $y, int $z, Block $block): void
    {
        $this->blocks[$x . ':' . $y . ':' . $z] = $block;
    }

    public function getChunk(int $chunkX, int $chunkZ): ?\pocketmine\world\format\Chunk
    {
        return $this->chunks[$chunkX . ':' . $chunkZ] ?? null;
    }

    public function setChunk(int $chunkX, int $chunkZ, \pocketmine\world\format\Chunk $chunk): void
    {
        $this->chunks[$chunkX . ':' . $chunkZ] = $chunk;
    }

    public function getMinY(): int { return -64; }
    public function getMaxY(): int { return 320; }
    public function isInWorld(int $x, int $y, int $z): bool { return $y >= -64 && $y < 320; }
}

$manager = new GeneratorManager();
$flat = $manager->getGenerator('flat') ?? throw new RuntimeException('flat generator missing');
$flat->validateGeneratorOptions('2;3*minecraft:stone,2*minecraft:dirt,minecraft:grass;1;village(size=1 distance=9)');

$options = FlatGeneratorOptions::parsePreset('2;stone,dirt,grass;1;decoration');
assert(count($options->getStructure()) === 3);

assert(Generator::convertSeed('') === null);
assert(Generator::convertSeed('123') === 123);
assert(Generator::convertSeed('abc') !== null);

$random = new Random(1234);
$tree = TreeFactory::get($random, TreeType::BIRCH) ?? throw new RuntimeException('birch tree missing');
$world = new SmokeChunkManager();
$transaction = $tree->getBlockTransaction($world, 0, 64, 0, $random);
assert($transaction !== null);
assert(iterator_count($transaction->getBlocks()) > 0);

$ore = new Ore(new Random(99), new OreType(new Block('minecraft:coal_ore', 'Coal Ore'), VanillaBlocks::STONE(), 1, 4, 0, 64));
$world->setBlockAt(1, 10, 1, VanillaBlocks::STONE());
$ore->placeObject($world, 1, 10, 1);
assert($ore->getType()->clusterSize === 4);

$gaussian = new Gaussian(2, 1.0);
assert(abs(array_sum($gaussian->getKernel()) - 1.0) < 0.000001);
assert(count($gaussian->getKernel()) === 5);

assert(Noise::linearLerp(0.5, 2.0, 4.0) === 3.0);
assert(Noise::bilinearLerp(0.5, 0.5, 0.0, 2.0, 2.0, 4.0) === 2.0);
$simplexA = new Simplex(123, 3, 0.5, 32.0);
$simplexB = new Simplex(123, 3, 0.5, 32.0);
assert($simplexA->getNoise2D(10.0, 20.0) === $simplexB->getNoise2D(10.0, 20.0));
assert($simplexA->getNoise3D(10.0, 20.0, 30.0) >= -1.0);
assert($simplexA->getNoise3D(10.0, 20.0, 30.0) <= 1.0);

$selector = new BiomeSelector(new Random(42));
$pickedBiome = $selector->pickBiome(16, 32);
assert($pickedBiome->getId() >= 0);
assert($selector->getTemperature(16, 32) >= 0.0 && $selector->getTemperature(16, 32) <= 1.0);
assert($selector->getRainfall(16, 32) >= 0.0 && $selector->getRainfall(16, 32) <= 1.0);

$cover = new GroundCover([VanillaBlocks::GRASS()]);
$coverWorld = new SmokeChunkManager();
$coverWorld->setBlockAt(0, 10, 0, VanillaBlocks::DIRT());
$cover->populate($coverWorld, 0, 0, new Random(7));
assert($coverWorld->getBlockAt(0, 11, 0)->hasSameTypeId(VanillaBlocks::GRASS()));

$orePopulator = new OrePopulator();
$orePopulator->setOreTypes([new OreType(new Block('minecraft:iron_ore', 'Iron Ore'), VanillaBlocks::STONE(), 2, 3, 0, 16)]);
$oreWorld = new SmokeChunkManager();
$oreWorld->setBlockAt(1, 5, 1, VanillaBlocks::STONE());
$orePopulator->populate($oreWorld, 0, 0, new Random(5));

$grassPopulator = new TallGrassPopulator();
$grassPopulator->setBaseAmount(1);
$grassPopulator->populate(new SmokeChunkManager(), 0, 0, new Random(8));

$explorerWorld = new SmokeChunkManager();
$explorerWorld->setChunk(0, 0, new \pocketmine\world\format\Chunk());
$explorer = new SubChunkExplorer($explorerWorld);
assert($explorer->moveTo(1, 1, 1) === SubChunkExplorerStatus::MOVED);
assert($explorer->moveTo(2, 2, 2) === SubChunkExplorerStatus::OK);
assert($explorer->isValid());
$explorer->invalidate();
assert(!$explorer->isValid());

$populationCompleted = false;
$populationTask = new PopulationTask(-64, 320, new Flat(42, ''), 0, 0, new \pocketmine\world\format\Chunk(), [], static function(mixed $center, array $adjacent) use (&$populationCompleted): void {
    $populationCompleted = $center instanceof \pocketmine\world\format\Chunk && $center->isPopulated() && $adjacent === [];
});
$populationTask->onRun();
$populationTask->onCompletion();
assert($populationCompleted);

$setup = new GeneratorExecutorSetupParameters(-64, 320, 42, Flat::class, '');
$register = new AsyncGeneratorRegisterTask(7, $setup);
$register->onRun();
assert(ThreadLocalGeneratorContext::fetch(7)?->getGenerator() instanceof Flat);
(new AsyncGeneratorUnregisterTask(7))->onRun();
assert(ThreadLocalGeneratorContext::fetch(7) === null);

$executor = new SyncGeneratorExecutor($setup);
$completed = false;
$executor->populate(0, 0, null, [], static function(mixed $center, array $adjacent) use (&$completed): void {
    $completed = $center !== null && $adjacent === [];
});
assert($completed);

echo "world-generator smoke ok\n";
