<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Services;

class SortService {

    /**
     * @param string $property
     * @param string $order
     *
     * @return callable
     */
    public function getSortableCallback(string $property, string $order): callable {
        switch($order) {
            case 'DESC':
                return $this->sortDescending($property);
            case 'ASC':
            default:
                return $this->sortAscending($property);
        }
    }

    /**
     * @param string $property
     *
     * @return callable
     */
    private function sortAscending(string $property): callable {
        return function($a, $b) use ($property) {
            return $a[$property] <=> $b[$property];
        };
    }

    /**
     * @param string $property
     *
     * @return callable
     */
    private function sortDescending(string $property): callable {
        return function($b, $a) use ($property) {
            return $a[$property] <=> $b[$property];
        };
    }

}