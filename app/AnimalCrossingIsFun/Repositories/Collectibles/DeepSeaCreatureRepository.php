<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\DeepSeaCreature as DeepSeaCreatureDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class DeepSeaCreatureRepository extends CreatureRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = DeepSeaCreatureDto::class;

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('deep_sea_creatures');

        return $this;
    }

    /**
     * @return $this
     */
    public function loadFiltersIntoData() {
        parent::loadCreatureFiltersIntoData([
            'shadow_size',
            'swimming_pattern',
        ]);

        return $this;
    }

    public function getFilters(): array {
        $filters = parent::getFilters();

        foreach($filters as $index => $filter) {
            if($filter['label'] === 'Location') {
                unset($filters[$index]);
            }
        }
        $filters = array_values($filters);

        $shadowSizes = array_unique(array_column($this->contents, 'shadow_size'));
        sort($shadowSizes);

        $filters[] = [
            'label'   => 'Shadow size',
            'filters' => $shadowSizes,
        ];

        $swimmingPatterns = array_unique(array_column($this->contents, 'swimming_pattern'));
        sort($swimmingPatterns);

        $filters[] = [
            'label'   => 'Swim. patterns',
            'filters' => $swimmingPatterns,
        ];

        return $filters;
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
            case 'swimming_pattern':
                $this->sortBySwimmingPatternAsc();
                break;
            case '-swimming_pattern':
                $this->sortBySwimmingPatternDesc();
                break;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sortBySwimmingPatternAsc() {
        $this->sort('swimming_pattern');

        return $this;
    }

    /**
     * @return $this
     */
    public function sortBySwimmingPatternDesc() {
        $this->sort('swimming_pattern', 'DESC');

        return $this;
    }

}