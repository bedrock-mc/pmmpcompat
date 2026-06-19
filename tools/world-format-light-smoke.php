<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\math\Vector3;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\HeightArray;
use pocketmine\world\format\LightArray;
use pocketmine\world\format\SubChunk;
use pocketmine\world\format\io\data\BedrockWorldData;
use pocketmine\world\format\io\exception\CorruptedChunkException;
use pocketmine\world\format\io\exception\CorruptedWorldException;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;
use pocketmine\world\format\ChunkException;
use pocketmine\world\light\BlockLightUpdate;
use pocketmine\world\light\LightPopulationTask;
use pocketmine\world\light\LightPropagationContext;
use pocketmine\world\light\SkyLightUpdate;

$height = HeightArray::fill(64);
assert($height->get(0, 0) === 64);
$height->set(15, 15, 72);
assert($height->get(15, 15) === 72);

$light = LightArray::fill(0);
$light->set(1, 2, 3, 14);
assert($light->get(1, 2, 3) === 14);
assert(strlen($light->getData()) === 2048);

$subChunk = new SubChunk();
assert($subChunk->isEmptyAuthoritative());
$subChunk->setBlockStateId(1, 2, 3, 99);
assert($subChunk->getBlockStateId(1, 2, 3) === 99);
assert($subChunk->getHighestBlockAt(1, 3) === 2);
$subChunk->setBlockLightArray($light);
assert($subChunk->getBlockLightArray()->get(1, 2, 3) === 14);

$chunk = new Chunk([0 => $subChunk], true);
assert($chunk->getSubChunk(0) === $subChunk);
assert($chunk->getBlockStateId(1, 2, 3) === 99);
assert($chunk->getHighestBlockAt(1, 3) === 2);
$chunk->setBiomeId(1, 2, 3, 7);
assert($chunk->getBiomeId(1, 2, 3) === 7);
$chunk->clearTerrainDirtyFlags();
assert(!$chunk->isTerrainDirty());
$chunk->setBlockStateId(1, 2, 3, 100);
assert($chunk->getTerrainDirtyFlag(Chunk::DIRTY_FLAG_BLOCKS));

$context = new LightPropagationContext();
assert($context->spreadQueue->isEmpty());
$blockLight = new BlockLightUpdate();
$blockLight->setAndUpdateLight(1, 2, 3, 9);
assert($blockLight->execute() === 1);
$skyLight = new SkyLightUpdate();
$skyLight->recalculateNode(1, 2, 3);
assert($skyLight->execute() === 1);

$completed = false;
$task = new LightPopulationTask($chunk, function (array $blockLightArrays, array $skyLightArrays, array $heightMap) use (&$completed): void {
    assert(isset($blockLightArrays[0], $skyLightArrays[0]));
    assert(count($heightMap) === 256);
    $completed = true;
});
$task->onRun();
$task->onCompletion();
assert($completed);

$path = tempnam(sys_get_temp_dir(), 'pmmpcompat-level-');
assert($path !== false);
file_put_contents($path, '');
$worldData = new BedrockWorldData($path);
$worldData->setName('Compat');
$worldData->setSpawn(new Vector3(1.2, 65.8, 2.4));
$worldData->setDifficulty(3);
$worldData->setRainLevel(0.5);
$worldData->save();
$loadedWorldData = new BedrockWorldData($path);
assert($loadedWorldData->getName() === 'Compat');
assert($loadedWorldData->getSpawn()->equals(new Vector3(1, 65, 2)));
assert($loadedWorldData->getDifficulty() === 3);
assert($loadedWorldData->getRainLevel() === 0.5);
unlink($path);

assert(new CorruptedChunkException('bad chunk') instanceof ChunkException);
assert(new CorruptedWorldException('bad world') instanceof \pocketmine\world\WorldException);
assert(new UnsupportedWorldFormatException('old world') instanceof \pocketmine\world\WorldException);

echo "world-format-light smoke ok\n";
