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
        switch($sort) {
            default:
                $this->sortByCategory();
                break;
            case 'name':
                $this->sortByNameAsc();
                break;
            case '-name':
                $this->sortByNameDesc();
                break;
            case 'price':
                $this->sortByPriceAsc();
                break;
            case '-price':
                $this->sortByPriceDesc();
                break;
        }

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