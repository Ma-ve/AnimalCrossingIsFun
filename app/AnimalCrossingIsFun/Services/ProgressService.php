<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use Mave\AnimalCrossingIsFun\Dto\Collectibles\Creature;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BugRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FishRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\FossilRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;

class ProgressService {

    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct() {
        $this->cacheService = new CacheService();
    }

    public function getAll(): array {
        $cacheKey = 'progress-items';
        $month = date('M');

        if(null !== ($result = $this->cacheService->get($cacheKey))) {
            return json_decode($result, true);
        }

        $items = [
            [
                'icon'  => 'fish',
                'label' => 'Fish',
                'group' => 'fish',
                'count' => count($fish = (new FishRepository(null))->loadAll()->getAll()),
            ],
            [
                'icon'  => 'bug',
                'label' => 'Bugs',
                'group' => 'bugs',
                'count' => count($bugs = (new BugRepository(null))->loadAll()->getAll()),
            ],
            [
                'icon'  => 'bone',
                'label' => 'Fossils',
                'group' => 'fossils',
                'count' => count($fossils = (new FossilRepository(null))->loadAll()->getAll()),
            ],
            [
                'icon'  => 'tools',
                'label' => 'Recipes',
                'group' => 'recipes',
                'count' => count($recipes = (new CherryBlossomRecipeRepository(null))->loadAll()->getAll()),
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

        $getSafeNames = function($items) {
            return array_map(function(Creature $item) {
                return $item->getSafeName();
            }, $items);
        };

        $seasonalItems = [
            'month' => date('F'),
            'items' => [
                [
                    'icon'  => 'fish',
                    'label' => 'Fish',
                    'group' => 'fish',
                    'count' => count($filteredFish),
                    'names' => $getSafeNames($filteredFish),
                ],
                [
                    'icon'  => 'bug',
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