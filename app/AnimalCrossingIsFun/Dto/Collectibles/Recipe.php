<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

class Recipe extends Collectible {

    protected const IMAGE_PATH = 'recipes';

    /** @var string[] */
    protected $requirements;

    /** @var string */
    protected $squareSize;

    /** @var string */
    protected $location;

    /** @var int */
    protected $price;

    /** @var string */
    protected $category;

    /** @var string */
    protected $group = 'recipes';

    /**
     * @return string[]
     */
    public function getRequirements(): array {
        return $this->requirements;
    }

    /**
     * @return string
     */
    public function getSquareSize(): string {
        return $this->squareSize;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getPrice(): int {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getCategory(): string {
        return $this->category;
    }

}
