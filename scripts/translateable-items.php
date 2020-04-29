<?php

namespace {

    use Mave\AnimalCrossingIsFun\Dto\Collectibles\Collectible;
    use Mave\AnimalCrossingIsFun\Dto\Language as LanguageDto;
    use Mave\AnimalCrossingIsFun\Repositories\LanguageRepository;

    require(__DIR__ . '/../vendor/autoload.php');

    if(empty($argv) || count($argv) === 1) {
        return;
    }

    $arguments = $argv;
    array_shift($arguments);

    /** @var LanguageDto[] $languages */
    $languages = (new LanguageRepository(null))
        ->loadAll()
        ->getAll();

    $currentTranslations = file_exists($translationsFile = __DIR__ . '/../data/translations.php')
        ? require($translationsFile)
        : [];

    $translations = [];
    foreach($arguments as $phpFile) {
        $phpFilePath = __DIR__ . '/../' . $phpFile;
        $data = require($phpFilePath);

        // /data/art.php -> art.php -> art
        $category = substr($phpFile, strrpos($phpFile, '/') + 1);
        $category = substr($category, 0, strpos($category, '.'));

        if('villagers' === $category) {
            continue;
        }

        foreach($languages as $language) {
            foreach($data as &$item) {
                $safeName = $item['safeName'] ?? false;
                if(!$safeName) {
                    echo "Could not find safeName in item " . json_encode($item) . " for category {$category}, skipping...\n";
                    continue;
                }
                $langCode = $language->getLangCode();

                $translatedItem = $currentTranslations[$category][$langCode][$safeName] ?? '';
                if($langCode === 'en') {
                    $translatedItem = $item['name'];
                }
                $translations[$category][$langCode][$safeName] = $translatedItem;
            }
        }
    }

    $varExport = var_export($translations, true);
    if(!file_put_contents($translationsFile, "<?php return {$varExport};")) {
        throw new Exception('Could not update translations file');
    }

    echo "Updated contents in '{$translationsFile}'" . PHP_EOL;
}
