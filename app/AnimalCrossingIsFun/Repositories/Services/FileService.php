<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Services;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;

abstract class FileService implements IDatabaseService {

    protected function getFilePath(string $origin): string {
        $filePath = BASE_PATH . 'data/' . $origin . static::getFileExtension();
        if(!file_exists($filePath)) {
            throw new Exception('No file found at location ' . $filePath);
        }

        return $filePath;
    }

    protected function getFileExtension() {
        throw new Exception('Must implement this in child class');
    }

}
