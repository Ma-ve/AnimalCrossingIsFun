<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/scripts',
        __DIR__ . '/src/index.php',
        __DIR__ . '/tests',
    ])
    // Target version is read from composer.json's "php" constraint
    ->withPhpSets()
    ->withComposerBased(phpunit: true)
    ->withSkip([
        // Test methods should stay void, even when they only mark themselves incomplete
        ReturnNeverTypeRector::class => [__DIR__ . '/tests'],
    ]);
