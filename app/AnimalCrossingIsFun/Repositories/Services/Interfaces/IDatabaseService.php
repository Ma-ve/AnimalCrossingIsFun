<?php

namespace Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces;

interface IDatabaseService {

    public function loadFromDatabase(string $origin): array;

}
