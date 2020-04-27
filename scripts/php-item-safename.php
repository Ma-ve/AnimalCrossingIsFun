<?php

namespace {

    use Mave\AnimalCrossingIsFun\Dto\Collectibles\Collectible;

    require(__DIR__ . '/../vendor/autoload.php');

    if(empty($argv) || count($argv) === 1) {
        return;
    }

    $arguments = $argv;
    array_shift($arguments);

    $allNames = [];
    foreach($arguments as $phpFile) {
        $canBeDuplicate = false;
        switch($phpFile) {
            case 'data/events.php':
                $canBeDuplicate = true;
                break;
        }

        $phpFilePath = __DIR__ . '/../' . $phpFile;
        $data = require($phpFilePath);

        foreach($data as &$item) {
            $safeName = Collectible::getSafeNameForString($item['name']);
            if(isset($allNames[$safeName]) && !$canBeDuplicate) {
                throw new Exception('Safe name ' . $safeName . ' already exists');
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
