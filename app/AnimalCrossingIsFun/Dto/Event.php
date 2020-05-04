<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

use Mave\AnimalCrossingIsFun\Dto\Traits\TimeLimitedTrait;

class Event extends Dto {
    use TimeLimitedTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $hemisphere;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $detailsLink;

    /**
     * @var string
     */
    protected $safeName;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getHemisphere(): string {
        return $this->hemisphere;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDetailsLink(): string {
        return $this->detailsLink;
    }

    /**
     * @return string
     */
    public function getSafeName(): string {
        return $this->safeName;
    }

}
