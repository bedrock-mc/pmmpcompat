<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use pocketmine\data\runtime\RuntimeDataReader;
use pocketmine\data\runtime\RuntimeDataSizeCalculator;
use pocketmine\data\runtime\RuntimeDataWriter;
use pocketmine\data\bedrock\block\upgrade\model\BlockStateUpgradeSchemaModel;
use pocketmine\data\bedrock\block\upgrade\model\BlockStateUpgradeSchemaModelFlattenInfo;
use pocketmine\data\bedrock\item\upgrade\ItemIdMetaUpgradeSchema;

enum SmokeRuntimeEnum
{
    case ZETA;
    case ALPHA;
    case BETA;
}

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$int = 5;
$bounded = 6;
$bool = true;
$enum = SmokeRuntimeEnum::BETA;
$enumSet = [
    spl_object_id(SmokeRuntimeEnum::ZETA) => SmokeRuntimeEnum::ZETA,
    spl_object_id(SmokeRuntimeEnum::BETA) => SmokeRuntimeEnum::BETA,
];

$size = new RuntimeDataSizeCalculator();
$size->int(3, $int);
$size->boundedIntAuto(2, 9, $bounded);
$size->bool($bool);
$size->enum($enum);
$size->enumSet($enumSet, SmokeRuntimeEnum::cases());
assertSameValue(12, $size->getBitsUsed(), 'unexpected runtime bit size');

$writer = new RuntimeDataWriter($size->getBitsUsed());
$writer->int(3, $int);
$writer->boundedIntAuto(2, 9, $bounded);
$writer->bool($bool);
$writer->enum($enum);
$writer->enumSet($enumSet, SmokeRuntimeEnum::cases());
assertSameValue(12, $writer->getOffset(), 'unexpected writer offset');

$readInt = 0;
$readBounded = 0;
$readBool = false;
$readEnum = SmokeRuntimeEnum::ALPHA;
$readEnumSet = [];

$reader = new RuntimeDataReader($size->getBitsUsed(), $writer->getValue());
$reader->int(3, $readInt);
$reader->boundedIntAuto(2, 9, $readBounded);
$reader->bool($readBool);
$reader->enum($readEnum);
$reader->enumSet($readEnumSet, SmokeRuntimeEnum::cases());

assertSameValue($int, $readInt, 'int round trip failed');
assertSameValue($bounded, $readBounded, 'bounded int round trip failed');
assertSameValue($bool, $readBool, 'bool round trip failed');
assertSameValue($enum, $readEnum, 'enum round trip failed');
assertSameValue($enumSet, $readEnumSet, 'enum set round trip failed');
assertSameValue(12, $reader->getOffset(), 'unexpected reader offset');

$itemSchema = new ItemIdMetaUpgradeSchema(
    ['minecraft:old_widget' => 'minecraft:new_widget'],
    ['minecraft:colored_widget' => [5 => 'minecraft:blue_widget']],
    7,
);
assertSameValue(7, $itemSchema->getSchemaId(), 'item schema id mismatch');
assertSameValue('minecraft:new_widget', $itemSchema->renameId('Minecraft:Old_Widget'), 'item rename failed');
assertSameValue('minecraft:blue_widget', $itemSchema->remapMeta('Minecraft:Colored_Widget', 5), 'item meta remap failed');
assertSameValue(null, $itemSchema->remapMeta('Minecraft:Colored_Widget', 6), 'missing item meta should not remap');

$flattenInfo = new BlockStateUpgradeSchemaModelFlattenInfo('pre_', 'variant', '_post', [], null);
assertSameValue(
    ['prefix' => 'pre_', 'flattenedProperty' => 'variant', 'suffix' => '_post'],
    $flattenInfo->jsonSerialize(),
    'flatten info JSON should omit empty remaps and null type',
);

$blockModel = new BlockStateUpgradeSchemaModel();
$blockModel->maxVersionMajor = 1;
$blockModel->maxVersionMinor = 20;
$blockModel->maxVersionPatch = 0;
$blockModel->maxVersionRevision = 0;
$blockModel->renamedIds = [];
$blockModel->addedProperties = [];
$blockModel->removedProperties = [];
$blockModel->renamedProperties = [];
$blockModel->remappedPropertyValues = [];
$blockModel->remappedPropertyValuesIndex = [];
$blockModel->flattenedProperties = ['minecraft:example' => $flattenInfo];
$blockModel->remappedStates = [];
assertSameValue(
    [
        'maxVersionMajor' => 1,
        'maxVersionMinor' => 20,
        'maxVersionPatch' => 0,
        'maxVersionRevision' => 0,
        'flattenedProperties' => ['minecraft:example' => $flattenInfo],
    ],
    $blockModel->jsonSerialize(),
    'block schema model JSON should omit empty sections',
);

echo "data runtime smoke ok\n";
