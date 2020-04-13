<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces;

interface IRepositoryDatabase {

    /**
     * @param string $fileName
     *
     * @return array
     */
    function loadFromDatabase(string $fileName): array;

}
