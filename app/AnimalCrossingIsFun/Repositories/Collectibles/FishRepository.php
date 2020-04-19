<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Fish as FishDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class FishRepository extends CreatureRepository implements IRepository {

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
        $this->contents = $this->databaseService->loadFromDatabase('fish');

        return $this;
    }

    /**
     * @return $this
     */
    public function loadFiltersIntoData() {
        parent::loadCreatureFiltersIntoData(['shadowSize']);

        return $this;
    }

    public function getFilters(): array {
        $filters = parent::getFilters();

        $shadowSizes = array_unique(array_column($this->contents, 'shadowSize'));
        sort($shadowSizes);

        $filters[] = [
            'label'   => 'Shadow size',
            'filters' => $shadowSizes,
        ];

        return $filters;
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