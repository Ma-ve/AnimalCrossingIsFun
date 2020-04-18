<?php

require(__DIR__ . '/../vendor/autoload.php');

if(empty($argv) || count($argv) === 1) {
    return;
}

$arguments = $argv;
array_shift($arguments);

foreach($arguments as $jsonFile) {
    $jsonFilePath = __DIR__ . '/../' . $jsonFile;
    $newFileName = str_replace('.json', '.php', $jsonFilePath);

    $varExport = var_export(json_decode(file_get_contents($jsonFilePath), true), true);
    if(!file_put_contents($newFileName, "<?php return {$varExport};")) {
        throw new Exception('Could not save json to php');
    }
    echo "Put contents to '{$newFileName}'" . PHP_EOL;
}
