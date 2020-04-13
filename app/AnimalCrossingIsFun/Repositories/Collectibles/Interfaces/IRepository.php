<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces;

interface IRepository {

    /**
     * @param string $name
     *
     * @return static
     */
    public function get(string $name);

    public function getAll(): array;

    /**
     * @return static
     */
    public function loadAll();

}
