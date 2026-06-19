<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Flat;
use pocketmine\world\generator\FlatGeneratorOptions;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\executor\AsyncGeneratorRegisterTask;
use pocketmine\world\generator\executor\AsyncGeneratorUnregisterTask;
use pocketmine\world\generator\executor\GeneratorExecutorSetupParameters;
use pocketmine\world\generator\executor\SyncGeneratorExecutor;
use pocketmine\world\generator\executor\ThreadLocalGeneratorContext;
use pocketmine\world\generator\object\Ore;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\object\TreeFactory;
use pocketmine\world\generator\object\TreeType;

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
