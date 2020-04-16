<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

abstract class Dto {

    /**
     * @param array $properties
     */
    public function __construct(array $properties = []) {
        foreach($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }

}
