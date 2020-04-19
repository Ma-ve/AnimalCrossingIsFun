<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\Fish as FishDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

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
        $this->contents = $this->databaseService->loadFromDatabase('fish');

        return $this;
    }

    /**
     * @return $this
     */
    public function loadFiltersIntoData() {
        $availableFilters = $this->getMonthFilters();
        foreach($this->contents as &$item) {
            foreach($availableFilters as $filter) {
                if($item[$filter['property']]) {
                    $item['filters'][] = $filter['label'];
                }
            }
            $item['filters'][] = $item['shadowSize'];
            $item['filters'][] = $item['location'];
        }

        return $this;
    }

    private function getMonthFilters(): array {
        return [
            [
                'property' => 'jan',
                'label'    => 'January',
            ],
            [
                'property' => 'feb',
                'label'    => 'February',
            ],
            [
                'property' => 'mar',
                'label'    => 'March',
            ],
            [
                'property' => 'apr',
                'label'    => 'April',
            ],
            [
                'property' => 'may',
                'label'    => 'May',
            ],
            [
                'property' => 'jun',
                'label'    => 'June',
            ],
            [
                'property' => 'jul',
                'label'    => 'July',
            ],
            [
                'property' => 'aug',
                'label'    => 'August',
            ],
            [
                'property' => 'sep',
                'label'    => 'September',
            ],
            [
                'property' => 'oct',
                'label'    => 'October',
            ],
            [
                'property' => 'nov',
                'label'    => 'November',
            ],
            [
                'property' => 'dec',
                'label'    => 'December',
            ],
        ];
    }

    public function getFilters(): array {
        $locations = array_unique(array_column($this->contents, 'location'));
        sort($locations);

        $shadowSizes = array_unique(array_column($this->contents, 'shadowSize'));
        sort($shadowSizes);

        return [
            [
                'label'   => 'Date',
                'filters' => array_column($this->getMonthFilters(), 'label'),
            ],
            [
                'label'   => 'Location',
                'filters' => $locations,
            ],
            [
                'label'   => 'Shadow size',
                'filters' => $shadowSizes,
            ],
        ];
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