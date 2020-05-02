<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use Mave\AnimalCrossingIsFun\Dto\Language as LanguageDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BaseRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class TranslationsRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = LanguageDto::class;

    /**
     * @return static
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('translations');

        return $this;
    }

    /**
     * @param string $langCode
     *
     * @return array
     */
    public function get(string $langCode) {
        $filtered = array_map(function($data) use ($langCode) {
            return $data[$langCode];
        }, $this->contents);

        unset(
            $filtered['housewares'],
            $filtered['equipments'],
            $filtered['others'],
            $filtered['tools']
        );

        return $filtered;
    }

}