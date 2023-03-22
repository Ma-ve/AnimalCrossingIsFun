<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Mave\AnimalCrossingIsFun\Dto\Collectibles\Collectible;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\ArtRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BugRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\DeepSeaCreatureRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FishRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FossilRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\SongRepository;

class ProgressService {

    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct() {
        $this->cacheService = new CacheService();
    }

    public function getAll(): array {
        $cacheKey = 'progress-items2';
        $month = date('M');

        if(null !== ($result = $this->cacheService->get($cacheKey))) {
            return json_decode($result, true);
        }

        $getSafeNames = function($items) {
            return array_map(function(Collectible $item) {
                return $item->getSafeName();
            }, $items);
        };

        $items = [
            [
                'icon'  => 'fad fa-fish',
                'label' => 'Fish',
                'group' => 'fish',
                'count' => count($fish = (new FishRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($fish),
            ],
            [
                'icon'  => 'fad fa-bug',
                'label' => 'Bugs',
                'group' => 'bugs',
                'count' => count($bugs = (new BugRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($bugs),
            ],
            [
                'icon'  => 'fad fa-bone',
                'label' => 'Fossils',
                'group' => 'fossils',
                'count' => count($fossils = (new FossilRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($fossils),
            ],
            [
                'icon'  => 'fab fa-octopus-deploy',
                'label' => 'Deep-Sea',
                'group' => 'deep-sea-creatures',
                'count' => count($deepSeaCreatures = (new DeepSeaCreatureRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($deepSeaCreatures),
            ],
            [
                'icon'  => 'fad fa-tools',
                'label' => 'Recipes',
                'group' => 'recipes',
                'count' => count($recipes = (new CherryBlossomRecipeRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($recipes),
            ],
            [
                'icon'  => 'fad fa-record-vinyl',
                'label' => 'Songs',
                'group' => 'songs',
                'count' => count($songs = (new SongRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($songs),
            ],
            [
                'icon'  => 'fad fa-palette',
                'label' => 'Art',
                'group' => 'art',
                'count' => count($recipes = (new ArtRepository(null))->loadAll()->getAll()),
                'names' => $getSafeNames($recipes),
            ],
        ];

        $monthSubstr = 'is' . substr($month, 0, 3);
        $filterItems = function($items) use ($monthSubstr) {
            return array_filter($items, function($item) use ($monthSubstr) {
                return $item->{$monthSubstr}();
            });
        };

        $filteredFish = $filterItems($fish);
        $filteredBugs = $filterItems($bugs);

        $seasonalItems = [
            'month' => date('F'),
            'items' => [
                [
                    'icon'  => 'fad fa-fish',
                    'label' => 'Fish',
                    'group' => 'fish',
                    'count' => count($filteredFish),
                    'names' => $getSafeNames($filteredFish),
                ],
                [
                    'icon'  => 'fad fa-bug',
                    'label' => 'Bugs',
                    'group' => 'bugs',
                    'count' => count($filteredBugs),
                    'names' => $getSafeNames($filteredBugs),
                ],
            ],
        ];

        $result = [
            'all'      => $items,
            'seasonal' => $seasonalItems,
        ];

        $this->cacheService->set($cacheKey, json_encode($result), 3600);

        return $result;
    }

}