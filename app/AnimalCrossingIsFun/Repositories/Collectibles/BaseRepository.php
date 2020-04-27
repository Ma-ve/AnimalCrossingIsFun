<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Dto;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;
use Mave\AnimalCrossingIsFun\Repositories\Services\PhpService;
use Mave\AnimalCrossingIsFun\Repositories\Services\SortService;

abstract class BaseRepository {

    /**
     * @var string
     */
    protected $dto;

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
        $this->databaseService = $databaseService ?? new PhpService();
        $this->sortService = new SortService();
    }

    /**
     * @param string $name
     *
     * @return bool|Dto
     */
    public function get(string $name) {
        $search = array_search($name, array_column($this->contents, 'safeName'));

        if(false === $search) {
            return false;
        }

        return new $this->dto($this->contents[$search]);
    }

    /**
     * @param string $name
     *
     * @return Dto[]
     */
    public function getMultipleBySingleKey(string $name) {
        $arrayKeys = array_keys(array_column($this->contents, 'safeName'), $name);

        if(empty($arrayKeys)) {
            return [];
        }

        return array_values(
            array_map(
                function($item) {
                    return new $this->dto($item);
                },
                array_intersect_key(
                    $this->contents,
                    array_flip($arrayKeys)
                ))
        );
    }

    public function getAll(): array {
        if(empty($this->contents)) {
            throw new Exception('Must first load data into repository');
        }

        return self::map(new $this->dto);
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
            case 'location':
                $this->sortByLocationAsc();
                break;
            case '-location':
                $this->sortByLocationDesc();
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

    /**
     * @return $this
     */
    public function loadFiltersIntoData() {
        return $this;
    }

    public function getFilters(): array {
        return [];
    }

}
