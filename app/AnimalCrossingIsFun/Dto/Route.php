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

}
