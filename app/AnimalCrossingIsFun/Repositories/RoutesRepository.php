<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use Mave\AnimalCrossingIsFun\Dto\MenuItem;
use Mave\AnimalCrossingIsFun\Dto\Route;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\ArtRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BugRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FishRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FossilRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\SongRepository;

class RoutesRepository {

    /**
     * @return MenuItem[]
     */
    public function getAll(): array {
        return [
            new MenuItem([
                'routes' => [
                    new Route([
                        'url'        => '/fish',
                        'twigView'   => 'pages/fish.twig',
                        'repository' => (new FishRepository(null)),
                        'icon'       => 'fa-fish',
                        'label'      => 'Fish',
                    ]),
                ],
            ]),
            new MenuItem([
                'routes' => [
                    new Route([
                        'url'        => '/bugs',
                        'twigView'   => 'pages/bugs.twig',
                        'repository' => (new BugRepository(null)),
                        'icon'       => 'fa-bug',
                        'label'      => 'Bugs',
                    ]),
                ],
            ]),
            new MenuItem([
                'routes' => [
                    new Route([
                        'url'        => '/fossils',
                        'twigView'   => 'pages/fossils.twig',
                        'repository' => (new FossilRepository(null)),
                        'icon'       => 'fa-bone',
                        'label'      => 'Fossils',
                    ]),
                ],
            ]),

            new MenuItem([
                'routes' => [
                    new Route($this->getRouteArtParams() + [
                            'cssClass' => 'd-none d-md-inline-block',
                        ]),
                ],
            ]),
            new MenuItem([
                'label'  => 'Misc.',
                'routes' => [
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
                    new Route([
                        'url'        => '/songs',
                        'twigView'   => 'pages/songs.twig',
                        'repository' => (new SongRepository(null)),
                        'icon'       => 'fa-record-vinyl',
                        'label'      => 'Songs',
                    ]),
                    new Route($this->getRouteArtParams() + [
                            'cssClass' => 'd-inline-block d-md-none',
                        ]),
                ],
            ]),
        ];
    }

    /**
     * @return array
     */
    private function getRouteArtParams() {
        return [
            'url'        => '/art',
            'twigView'   => 'pages/art.twig',
            'repository' => (new ArtRepository(null)),
            'icon'       => 'fa-palette',
            'label'      => 'Art',
        ];
    }

}
