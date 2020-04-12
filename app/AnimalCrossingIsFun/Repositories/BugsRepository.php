<?php

namespace Mave\AnimalCrossingIsFun\Repositories;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Dto\Bug as BugDto;
use Mave\AnimalCrossingIsFun\Repositories\Interfaces\IRepository;

class BugsRepository extends BaseRepository implements IRepository {

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
        $this->contents = $this->databaseService->loadFromDatabase('bugs.json');

        return $this;
    }

}