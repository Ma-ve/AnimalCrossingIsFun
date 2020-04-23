<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Fossil as FossilDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class FossilRepository extends BaseRepository implements IRepository {

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
        return parent::map(new FossilDto());
    }

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