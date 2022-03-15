<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

class User extends Dto {

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    public function getId(): string {
        return $this->id;
    }

    public function getUsername(): string {
        return (string)$this->username;
    }

}
