<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Fossil as FossilDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class FossilRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    #[\Override]
    protected $dto = FossilDto::class;

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('fossils');

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