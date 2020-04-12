<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Services;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;

class JsonService implements IDatabaseService {

    public function loadFromDatabase(string $origin): array {
        $filePath = BASE_PATH . 'data/' . $origin;
        if(!file_exists($filePath)) {
            throw new Exception('No file found at location ' . $filePath);
        }

        $json = json_decode(file_get_contents($filePath), true);
        if(!$json || json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('No valid JSON in ' . $filePath);
        }

        return $json;
    }

}
