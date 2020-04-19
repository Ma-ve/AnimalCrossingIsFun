<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

abstract class CreatureRepository extends BaseRepository {

    /**
     * @param array $properties
     *
     * @return $this
     */
    protected function loadCreatureFiltersIntoData(array $properties = []) {
        $availableFilters = $this->getMonthFilters();
        foreach($this->contents as &$item) {
            foreach($availableFilters as $filter) {
                if($item[$filter['property']]) {
                    $item['filters'][] = $filter['label'];
                }
            }
            $item['filters'][] = $item['location'];
            foreach($properties as $property) {
                $item['filters'][] = $item[$property];
            }
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

        return [
            [
                'label'   => 'Date',
                'filters' => array_column($this->getMonthFilters(), 'label'),
            ],
            [
                'label'   => 'Location',
                'filters' => $locations,
            ],
        ];
    }

}
