<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BaseRepository;

class Route extends Dto {

    /** @var string */
    protected $url;

    /** @var string */
    protected $twigView;

    /** @var BaseRepository */
    protected $repository;

    /** @var string */
    protected $icon;

    /** @var string */
    protected $label;

    /** @var string */
    protected $cssClass;

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTwigView(): string {
        return $this->twigView;
    }

    /**
     * @return BaseRepository
     */
    public function getRepository(): BaseRepository {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getIcon(): string {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getCssClass(): ?string {
        return $this->cssClass;
    }

}
