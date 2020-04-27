<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use DateTime;
use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Song as SongDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

/**
 * @method getAll()
 */
class SongRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = SongDto::class;

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('songs');

        return $this;
    }

}