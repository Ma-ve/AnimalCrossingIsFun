<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

class Fish extends Creature {

    /** @var string */
    protected $location;

    /** @var int */
    protected $shadowSize;

    /** @var string */
    protected $group = 'fish';

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getShadowSize(): int {
        return (int)$this->shadowSize;
    }

}
