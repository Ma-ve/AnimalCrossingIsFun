<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Dto;

use Exception;

abstract class Dto {

    protected const IMAGE_PATH = 'random';

    /** @var string */
    protected $name;

    /** @var string */
    protected $imageLink;

    /**
     * @param array $properties
     */
    public function __construct(array $properties = []) {
        foreach($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }

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
        $file = str_replace(" ", '-', strtolower($this->name));

        $path = static::IMAGE_PATH;

        return "/img/{$path}/{$file}.png";
    }

}
