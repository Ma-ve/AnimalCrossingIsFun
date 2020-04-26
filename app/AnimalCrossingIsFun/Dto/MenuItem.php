<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

class MenuItem extends Dto {

    /** @var string */
    protected $icon;

    /** @var string */
    protected $label;

    /** @var Route[] */
    protected $routes;

    /**
     * @return string
     */
    public function getIcon(): ?string {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array {
        return $this->routes;
    }

}
