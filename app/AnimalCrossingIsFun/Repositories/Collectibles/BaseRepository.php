<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Mave\AnimalCrossingIsFun\Dto\Dto;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;
use Mave\AnimalCrossingIsFun\Repositories\Services\JsonService;
use Mave\AnimalCrossingIsFun\Repositories\Services\SortService;

abstract class BaseRepository {

    /**
     * @var array
     */
    protected $contents;

    /** @var IDatabaseService */
    protected $databaseService;

    /**
     * @var SortService
     */
    private $sortService;

    /**
     * @param IDatabaseService $databaseService
     */
    public function __construct(?IDatabaseService $databaseService) {
        $this->databaseService = $databaseService ?? new JsonService();
        $this->sortService = new SortService();
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
     * @param Dto $dtoClass
     *
     * @return Dto[]
     */
    protected function map(Dto $dtoClass) {
        return array_map(function($item) use ($dtoClass) {
            return new $dtoClass($item);
        }, $this->contents);
    }

    /**
     * @param string $property
     * @param string $order
     *
     * @return static
     */
    protected function sort(string $property, string $order = 'ASC') {
        uasort($this->contents, $this->sortService->getSortableCallback($property, $order));

        return $this;
    }

    /**
     * @return $this
     */
    public function sortByNameAsc() {
        $this->sort('name');

        return $this;
    }

    /**
     * @return $this
     */
    public function sortByNameDesc() {
        $this->sort('name', 'DESC');

        return $this;
    }

    /**
     * @return $this
     */
    public function sortByPriceAsc() {
        $this->sort('price');

        return $this;
    }

    /**
     * @return $this
     */
    public function sortByPriceDesc() {
        $this->sort('price', 'DESC');

        return $this;
    }


}
