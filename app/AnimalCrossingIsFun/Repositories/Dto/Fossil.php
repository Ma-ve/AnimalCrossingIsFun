<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Dto;

class Fossil extends Dto {

    protected const IMAGE_PATH = 'fossils';

    /** @var int */
    protected $price;

    /** @var bool */
    protected $isMultiPart;

    /** @var string */
    protected $category;

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
