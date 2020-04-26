<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Tests;

use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;

class MockDatabaseService implements IDatabaseService {

    /**
     * @var array
     */
    private $contents;

    /**
     * @param array $data
     */
    public function __construct(array $data) {
        $this->contents = $data;
    }

    public function loadFromDatabase(string $origin): array {
        return $this->contents;
    }

}
