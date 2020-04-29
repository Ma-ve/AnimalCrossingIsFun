<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

class Language extends Dto {

    /**
     * @var string
     */
    protected $langCode;

    /**
     * @var string
     */
    protected $label;

    /**
     * @return string
     */
    public function getLangCode(): string {
        return $this->langCode;
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

}
