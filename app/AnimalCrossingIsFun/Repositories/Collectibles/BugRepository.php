<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Bug as BugDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class BugRepository extends CreatureRepository implements IRepository {

    /**
     * @param string $name
     *
     * @return bool|mixed
     */
    public function get(string $name) {
        return $this->contents[$name] ?? false;
    }

    public function getAll(): array {
        if(empty($this->contents)) {
            throw new Exception('Must first load data into repository');
        }
        return parent::map(new BugDto());
    }

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