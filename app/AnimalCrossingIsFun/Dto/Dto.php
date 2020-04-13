<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

use Exception;

abstract class Dto {

    protected const IMAGE_PATH = 'random';

    /** @var string */
    protected $name;

    /** @var string */
    protected $imageLink;

    /** @var string */
    protected $safeName;

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
        $path = static::IMAGE_PATH;

        return "/img/{$path}/{$this->getSafeName()}.png";
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSafeName(): string {
        return str_replace('--', '-', strtolower(preg_replace("/[^\da-z]/i", "-", $this->name)));
    }

}
