<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Recipe as RecipeDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class RecipeRepository extends BaseRepository implements IRepository {

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
        return parent::map(new RecipeDto());
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = [];

        return $this;
    }

}