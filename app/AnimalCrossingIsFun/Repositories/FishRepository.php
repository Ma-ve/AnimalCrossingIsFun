<?php

namespace Mave\AnimalCrossingIsFun\Repositories;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Dto\Fish as FishDto;
use Mave\AnimalCrossingIsFun\Repositories\Interfaces\IRepository;

class FishRepository extends BaseRepository implements IRepository {

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
        return parent::map(new FishDto());
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('fish.json');

        return $this;
    }

    /**
     * @param bool|string $sort
     *
     * @return $this
     */
    public function sortItems($sort = false) {
        switch($sort) {
            case 'name':
            default:
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

    /**
     * @return $this
     */
    public function sortByShadowSizeAsc() {
        $this->sort('shadowSize');

        return $this;
    }

    /**
     * @return $this
     */
    public function sortByShadowSizeDesc() {
        $this->sort('shadowSize', 'DESC');

        return $this;
    }

}