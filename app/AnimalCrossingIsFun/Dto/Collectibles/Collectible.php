<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Dto;

abstract class Collectible extends Dto {

    protected const IMAGE_PATH = 'random';

    /** @var string */
    protected $name;

    /** @var string */
    protected $imageLink;

    /** @var null|string */
    protected $group;

    /** @var string */
    protected $safeName;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getImageLink(): string {
        $path = static::IMAGE_PATH;

        return "/img/{$path}/{$this->getSafeName()}.png";
    }

    public function getGroup(): ?string {
        return $this->group;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSafeName(): string {
        return str_replace('--', '-', strtolower(preg_replace("/[^\da-z]/i", "-", $this->name)));
    }

}
