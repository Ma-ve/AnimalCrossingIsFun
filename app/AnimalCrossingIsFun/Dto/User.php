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

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

}
