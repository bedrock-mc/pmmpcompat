<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\KnownTranslationKeys;
use pocketmine\lang\Language;
use pocketmine\lang\LanguageNotFoundException;
use pocketmine\lang\Translatable;
use pocketmine\timings\Timings;
use pocketmine\timings\TimingsHandler;
use pocketmine\timings\TimingsRecord;

assert(KnownTranslationKeys::CHAT_TYPE_TEXT === 'chat.type.text');
$translation = KnownTranslationFactory::chat_type_text('Steve', 'hello');
assert($translation instanceof Translatable);
assert($translation->getText() === 'chat.type.text');
assert($translation->getParameter(0) === 'Steve');

$dir = sys_get_temp_dir() . '/pmmpcompat-lang-' . getmypid();
@mkdir($dir, 0777, true);
file_put_contents($dir . '/eng.ini', "language.name=English\nchat.type.text=<{%0}> {%1}\ncustom.key=Custom {%0}\n");
file_put_contents($dir . '/spa.ini', "language.name=Spanish\ncustom.key=Personal {%0}\n");
assert(Language::getLanguageList($dir) === ['eng' => 'English', 'spa' => 'Spanish']);
$language = new Language('spa', $dir);
assert($language->getName() === 'Spanish');
assert($language->getLang() === 'spa');
assert($language->get('missing.key') === 'missing.key');
assert($language->translateString('custom.key', ['A']) === 'Personal A');
assert($language->translate(KnownTranslationFactory::chat_type_text('Alex', 'hi')) === '<Alex> hi');
assert($language->translateString('%custom.key', ['B']) === 'Personal B');
try {
    new Language('missing', $dir);
    assert(false);
} catch (LanguageNotFoundException) {
}

Timings::init();
assert(Timings::$fullTick instanceof TimingsHandler);
$handler = Timings::getCommandDispatchTimings('test');
assert($handler->getName() === 'Command: test');
$record = new TimingsRecord($handler);
$record->startTiming(100);
$record->stopTiming(160);
assert($record->getCount() === 1);
assert($record->getTotalTime() === 60.0);
TimingsRecord::tick();
assert($record->getTicksActive() === 1);
assert(TimingsRecord::getAll() !== []);
TimingsRecord::reset();
assert(TimingsRecord::getAll() === []);

echo "pmmpcompat lang/timings smoke ok\n";
