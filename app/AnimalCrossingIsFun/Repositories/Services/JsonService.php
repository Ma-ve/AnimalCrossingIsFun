<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Services;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;

class JsonService extends FileService implements IDatabaseService {

    public function loadFromDatabase(string $origin): array {
        $json = json_decode(file_get_contents(parent::getFilePath($origin)), true);
        if(!$json || json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('No valid JSON for origin ' . $origin);
        }

        return $json;
    }

    /**
     * @return string
     */
    protected function getFileExtension(): string {
        return '.json';
    }

}
