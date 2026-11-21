<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Art as ArtDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class ArtRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    #[\Override]
    protected $dto = ArtDto::class;

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('art');

        return $this;
    }

    /**
     * @param bool|string $sort
     *
     * @return $this
     */
    public function sortItems($sort = false) {
        match ($sort) {
            'name' => $this->sortByNameAsc(),
            '-name' => $this->sortByNameDesc(),
            'price' => $this->sortByPriceAsc(),
            '-price' => $this->sortByPriceDesc(),
            default => $this->sortByCategory(),
        };

        return $this;
    }

    private function sortByCategory() {
        usort($this->contents, function($a, $b) {
            $aSort = $a['category'] ?? $a['name'];
            $bSort = $b['category'] ?? $b['name'];

            return $aSort <=> $bSort;
        });
    }

}