<?php

namespace {

    use Mave\AnimalCrossingIsFun\Dto\Collectibles\Collectible;

    require(__DIR__ . '/../vendor/autoload.php');

    if(empty($argv) || count($argv) === 1) {
        return;
    }

    $overrideSafeNames = [
        'data/villagers.php' => [
            'Anchovy' => 'anchovy-villager',
            'Rocket' => 'rocket-villager',
        ],
    ];

    $arguments = $argv;
    array_shift($arguments);

    $allNames = [];
    foreach($arguments as $phpFile) {
        $canBeDuplicate = false;
        switch($phpFile) {
            case 'data/events.php':
                $canBeDuplicate = true;
                break;
            case 'data/translations.php':
                continue 2;
        }

        $phpFilePath = __DIR__ . '/../' . $phpFile;
        $data = require($phpFilePath);

        foreach($data as $key => &$item) {
            $name = $item['name'] ?? $key;
            $safeName = $overrideSafeNames[$phpFile][$name] ?? Collectible::getSafeNameForString($name);
            if(isset($allNames[$safeName]) && !$canBeDuplicate) {
                throw new Exception(sprintf('Safe name %s already exists (for %s (%s))', $safeName, $name, $phpFile));
            }
            $allNames[$safeName] = true;
            $item['safeName'] = $safeName;
        }

        $varExport = var_export($data, true);
        if(!file_put_contents($phpFilePath, "<?php return {$varExport};")) {
            throw new Exception('Could not save json to php');
        }
        echo "Updated contents in '{$phpFilePath}'" . PHP_EOL;
    }

}
