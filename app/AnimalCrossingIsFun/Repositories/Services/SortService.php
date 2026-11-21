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
        return match ($order) {
            'DESC' => $this->sortDescending($property),
            default => $this->sortAscending($property),
        };
    }

    /**
     * @param string $property
     *
     * @return callable
     */
    private function sortAscending(string $property): callable {
        return fn($a, $b) => $a[$property] <=> $b[$property];
    }

    /**
     * @param string $property
     *
     * @return callable
     */
    private function sortDescending(string $property): callable {
        return fn($b, $a) => $a[$property] <=> $b[$property];
    }

}