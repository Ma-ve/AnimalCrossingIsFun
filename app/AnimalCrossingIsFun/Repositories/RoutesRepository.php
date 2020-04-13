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
                'icon'       => 'fa-fish',
                'label'      => 'Fish',
            ]),
            new Route([
                'url'        => '/bugs',
                'twigView'   => 'pages/bugs.twig',
                'repository' => (new BugsRepository(null)),
                'icon'       => 'fa-bug',
                'label'      => 'Bugs',
            ]),
            new Route([
                'url'        => '/fossils',
                'twigView'   => 'pages/fossils.twig',
                'repository' => (new FossilsRepository(null)),
                'icon'       => 'fa-bone',
                'label'      => 'Fossils',
            ]),
        ];
    }

}
