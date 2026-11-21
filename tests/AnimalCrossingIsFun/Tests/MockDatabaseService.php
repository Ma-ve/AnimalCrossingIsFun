<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Tests;

use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;

class MockDatabaseService implements IDatabaseService {

    /**
     * @param array $contents
     */
    public function __construct(private readonly array $contents)
    {
    }

    public function loadFromDatabase(string $origin): array {
        return $this->contents;
    }

}
