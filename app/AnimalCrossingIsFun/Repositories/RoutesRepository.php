<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use Mave\AnimalCrossingIsFun\Dto\Route;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\ArtRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BugRepository;use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FishRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FossilRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;

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
                'repository' => (new BugRepository(null)),
                'icon'       => 'fa-bug',
                'label'      => 'Bugs',
            ]),
            new Route([
                'url'        => '/fossils',
                'twigView'   => 'pages/fossils.twig',
                'repository' => (new FossilRepository(null)),
                'icon'       => 'fa-bone',
                'label'      => 'Fossils',
            ]),
            new Route([
                'url'        => '/art',
                'twigView'   => 'pages/art.twig',
                'repository' => (new ArtRepository(null)),
                'icon'       => 'fa-palette',
                'label'      => 'Art',
            ]),
            new Route([
                'url'        => '/recipes/cherry-blossom-season',
                'twigView'   => 'pages/recipes.twig',
                'repository' => (new CherryBlossomRecipeRepository(null)),
                'icon'       => 'fa-tools',
                'label'      => 'Recipes',
            ]),
            new Route([
                'url'        => '/events',
                'twigView'   => 'pages/events.twig',
                'repository' => (new EventRepository(null)),
                'icon'       => 'fa-glass-cheers',
                'label'      => 'Events',
            ]),
        ];
    }

}
