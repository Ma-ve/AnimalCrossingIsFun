<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

class Fossil extends Collectible {

    protected const IMAGE_PATH = 'fossils';

    /** @var int */
    protected $price;

    /** @var bool */
    protected $isMultiPart;

    /** @var string */
    protected $category;

    /** @var string */
    protected $group = 'fossils';

    /**
     * @return bool
     */
    public function isMultiPart(): bool {
        return $this->isMultiPart;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string {
        return $this->category;
    }

    /**
     * @return int
     */
    public function getPrice(): int {
        return $this->price;
    }

}
