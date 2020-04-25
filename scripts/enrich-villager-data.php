<?php

namespace {

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
            $dt = DateTime::createFromFormat('F jS', trim($item['birthday']));
            $item['date'] = $dt->format('m-d');

            $exploded = explode(' ', $item['personality']);
            $item['gender'] = $exploded[0] === '♂' ? 'Male' : 'Female';
            $item['personality'] = $exploded[1];

        }

        $varExport = var_export($data, true);

        if(!file_put_contents($phpFilePath, "<?php return {$varExport};")) {
            throw new Exception('Could not update birthdays');
        }
        echo "Updated contents in '{$phpFilePath}'" . PHP_EOL;
    }

}
