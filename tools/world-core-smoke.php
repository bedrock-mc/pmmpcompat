<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\NotCloneable;
use pocketmine\world\ChunkListener;
use pocketmine\world\ChunkListenerNoOpTrait;
use pocketmine\world\ChunkLockId;
use pocketmine\world\ChunkManager;
use pocketmine\world\ChunkTicker;
use pocketmine\world\SimpleChunkManager;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use pocketmine\world\WorldException;
use pocketmine\world\format\Chunk;
use pocketmine\world\utils\SubChunkExplorerStatus;

$options = WorldCreationOptions::create()
    ->setSeed(12345)
    ->setDifficulty(World::DIFFICULTY_HARD)
    ->setGeneratorOptions('preset')
    ->setSpawnPosition(new Vector3(1, 65, 2));

assert($options->getSeed() === 12345);
assert($options->getDifficulty() === World::DIFFICULTY_HARD);
assert($options->getGeneratorOptions() === 'preset');
assert($options->getSpawnPosition()->equals(new Vector3(1, 65, 2)));

$manager = new SimpleChunkManager(World::Y_MIN, World::Y_MAX);
assert($manager instanceof ChunkManager);
assert($manager->isInWorld(0, 64, 0));
assert(!$manager->isInWorld(0, World::Y_MAX, 0));
assert($manager->getBlockAt(0, 64, 0)->getTypeId() === 'minecraft:air');
$manager->setBlockAt(0, 64, 0, VanillaBlocks::STONE());
assert($manager->getBlockAt(0, 64, 0)->getTypeId() === 'minecraft:stone');
$chunk = new Chunk();
$manager->setChunk(0, 0, $chunk);
assert($manager->getChunk(0, 0) === $chunk);
$manager->cleanChunks();
assert($manager->getChunk(0, 0) === null);
assert($manager->getBlockAt(0, 64, 0)->getTypeId() === 'minecraft:air');

$listener = new class implements ChunkListener {
    use ChunkListenerNoOpTrait;
};
$listener->onChunkLoaded(0, 0, new Chunk());
$listener->onBlockChanged(new Vector3(0, 64, 0));

assert(new ChunkTicker() instanceof ChunkTicker);
assert(new ChunkLockId() instanceof ChunkLockId);
assert(in_array(NotCloneable::class, class_uses(ChunkLockId::class), true));
assert(new WorldException('world error') instanceof \RuntimeException);
assert(SubChunkExplorerStatus::INVALID === 0);
assert(SubChunkExplorerStatus::OK === 1);
assert(SubChunkExplorerStatus::MOVED === 2);

echo "world-core smoke ok\n";
