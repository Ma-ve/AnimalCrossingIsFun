<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use Mave\AnimalCrossingIsFun\Dto\Route;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BugsRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FishRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FossilsRepository;

class RoutesRepository {

    /**
     * @return Route[]
     */
    public function getAll(): array {
        return [
            new Route([
                'url'        => '/fish',
                'twigView'   => 'pages/fish.twig',
                'repository' => (new FishRepository(null)),
            ]),
            new Route([
                'url'        => '/bugs',
                'twigView'   => 'pages/bugs.twig',
                'repository' => (new BugsRepository(null)),
            ]),
            new Route([
                'url'        => '/fossils',
                'twigView'   => 'pages/fossils.twig',
                'repository' => (new FossilsRepository(null)),
            ]),
        ];
    }

}
