<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

class Art extends Collectible {

    protected const IMAGE_PATH = 'art';

    /** @var int */
    protected $price = -1;

    /** @var bool */
    protected $canBeFake;

    /** @var string */
    protected $category;

    /** @var string */
    #[\Override]
    protected $group = 'art';

    /**
     * @return bool
     */
    public function canBeFake(): bool {
        return $this->canBeFake;
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

    /**
     * @TODO: get proper images
     *
     * @return string
     */
    public function getImageLink(): string {
        $placeholder = match ($this->category) {
            'Painting' => 'painting',
            'Statue' => 'statue',
            default => 'unknown',
        };
        $path = static::IMAGE_PATH;

        return "/img/{$path}/{$placeholder}.png";
    }

}
