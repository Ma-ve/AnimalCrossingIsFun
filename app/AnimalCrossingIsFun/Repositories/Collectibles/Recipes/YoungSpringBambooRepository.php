<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\RecipeRepository;

class YoungSpringBambooRepository extends RecipeRepository implements IRepository {

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = [
            [
                'name'         => 'Pile of leaves',
                'requirements' => [
                    '1x young spring bamboo',
                    '10x weeds',
                ],
                'squareSize'   => '1x1',
                'location'     => '?',
                'price'        => -1,
                'category'     => 'Young spring bamboo',
                'safeName'     => 'pile-of-leaves',
            ],

            [
                'name'         => 'Bamboo Lamp',
                'requirements' => [
                    '4x young spring bamboo',
                    '5x bamboo',
                    '4x clay',
                ],
                'squareSize'   => '1x1',
                'location'     => '?',
                'price'        => -1,
                'category'     => 'Young spring bamboo',
                'safeName'     => 'bamboo-lamp',
            ],

            [
                'name'         => 'Bamboo Noodle Slide',
                'requirements' => [
                    '7x young spring bamboo',
                    '3x wood',
                ],
                'squareSize'   => '3x1',
                'location'     => '?',
                'price'        => -1,
                'category'     => 'Young spring bamboo',
                'safeName'     => 'bamboo-noodle-slide',
            ],
        ];

        return $this;
    }

}