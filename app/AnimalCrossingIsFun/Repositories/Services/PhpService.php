<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Services;

use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;

class PhpService extends FileService implements IDatabaseService {

    public function loadFromDatabase(string $origin): array {
        return require(parent::getFilePath($origin));
    }

    /**
     * @return string
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getFileExtension() {
        return '.php';
    }

}
