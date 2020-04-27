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
        $phpFilePath = __DIR__ . '/../' . $phpFile;
        $data = require($phpFilePath);

        foreach($data as &$item) {
            $suffix = '';
            $safeName = Collectible::getSafeNameForString($item['name']);
            if(isset($allNames[$safeName])) {
                echo "Safe name '{$safeName}' already exists... ";
                $suffix = "-" . (string)++$allNames[$safeName];
                echo ", suffixing it... New name: '{$safeName}{$suffix}'\n";
            } else {
                $allNames[$safeName] = 1;
            }
            $item['safeName'] = $safeName . $suffix;
        }

        $varExport = var_export($data, true);
        if(!file_put_contents($phpFilePath, "<?php return {$varExport};")) {
            throw new Exception('Could not save json to php');
        }
        echo "Updated contents in '{$phpFilePath}'" . PHP_EOL;
    }

}
