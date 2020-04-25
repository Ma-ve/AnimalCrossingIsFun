<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Bug as BugDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class BugRepository extends CreatureRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = BugDto::class;

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('bugs');

        return $this;
    }

    /**
     * @return $this
     */
    public function loadFiltersIntoData() {
        parent::loadCreatureFiltersIntoData([]);

        return $this;
    }

}