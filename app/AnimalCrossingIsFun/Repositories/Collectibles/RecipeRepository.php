<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Recipe as RecipeDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class RecipeRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = RecipeDto::class;

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = [];

        return $this;
    }

}