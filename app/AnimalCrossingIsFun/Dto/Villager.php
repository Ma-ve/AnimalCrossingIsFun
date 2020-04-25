<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

class Villager extends Dto {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $personality;

    /**
     * @var string
     */
    protected $species;

    /**
     * @var string
     */
    protected $birthday;

    /**
     * @var string
     */
    protected $catchPhrase;

    /**
     * @var string
     */
    protected $date;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPersonality(): string {
        return $this->personality;
    }

    /**
     * @return string
     */
    public function getSpecies(): string {
        return $this->species;
    }

    /**
     * @return string
     */
    public function getBirthday(): string {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getCatchPhrase(): string {
        return $this->catchPhrase;
    }

    /**
     * @return string
     */
    public function getDate(): string {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getGender(): string {
        return $this->gender;
    }

}
