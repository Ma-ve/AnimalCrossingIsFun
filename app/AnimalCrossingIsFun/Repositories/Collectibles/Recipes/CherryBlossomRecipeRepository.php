<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes;

use Exception;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\RecipeRepository;

class CherryBlossomRecipeRepository extends RecipeRepository implements IRepository {

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = [
            [
                'name'         => 'Blossom-viewing lantern',
                'requirements' => [
                    '6x cherry-blossom petal',
                    '4x hardwood',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2880,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'blossom-viewing-lantern',
            ],

            [
                'name'         => 'Cherry-blossom bonsai',
                'requirements' => [
                    '6x cherry-blossom petal',
                    '2x hardwood',
                    '3x clump of weeds',
                    '3x clay',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 3300,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-bonsai',
            ],

            [
                'name'         => 'Cherry-blossom branches',
                'requirements' => [
                    '8x cherry-blossom petal',
                    '4x tree branch',
                    '5x clay',
                ],
                'squareSize'   => '1.5x1.5',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 4240,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-branches',
            ],

            [
                'name'         => 'Cherry-blossom clock',
                'requirements' => [
                    '5x cherry-blossom petal',
                    '1x iron nugget',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2750,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-clock',
            ],

            [
                'name'         => 'Cherry-blossom flooring',
                'requirements' => [
                    '10x cherry-blossom petal',
                    '20x clump of weeds',
                ],
                'squareSize'   => 'N/A',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 4400,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-flooring',
            ],

            [
                'name'         => 'Cherry-blossom-petal pile',
                'requirements' => [
                    '5x cherry-blossom petal',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2000,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-petal-pile',
            ],

            [
                'name'         => 'Cherry-blossom pochette',
                'requirements' => [
                    '6x cherry-blossom petal',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2400,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-pochette',
            ],

            [
                'name'         => 'Cherry-blossom pond stone',
                'requirements' => [
                    '10x stone',
                    '3x cherry-blossom petal',
                ],
                'squareSize'   => '2x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2700,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-pond-stone',
            ],

            [
                'name'         => 'Cherry-blossom-trees wall',
                'requirements' => [
                    '10x cherry-blossom petal',
                    '5x hardwood',
                ],
                'squareSize'   => 'N/A',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 4600,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-trees-wall',
            ],

            [
                'name'         => 'Cherry-blossom umbrella',
                'requirements' => [
                    '7x cherry-blossom petal',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2800,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-umbrella',
            ],

            [
                'name'         => 'Cherry-blossom wand',
                'requirements' => [
                    '3x cherry-blossom petal',
                    '3x star fragment',
                ],
                'squareSize'   => '1x1',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 2700,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'cherry-blossom-wand',
            ],

            [
                'name'         => 'Outdoor picnic set',
                'requirements' => [
                    '10x cherry-blossom petal',
                ],
                'squareSize'   => '1.5x1.5',
                'location'     => 'Balloon (Cherry-blossom season) / Isabelle announcement (Cherry-blossom season)',
                'price'        => 4000,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'putdoor-picnic-set',
            ],

            [
                'name'         => 'Sakura-wood flooring',
                'requirements' => [
                    '5x cherry-blossom petal',
                    '10x wood',
                ],
                'squareSize'   => 'N/A',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 3200,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'sakura-wood-flooring',
            ],

            [
                'name'         => 'Sakura-wood wall',
                'requirements' => [
                    '5x cherry-blossom petal',
                    '10x wood',
                ],
                'squareSize'   => 'N/A',
                'location'     => 'Balloon (Cherry-blossom season)',
                'price'        => 3200,
                'category'     => 'Cherry-blossom season',
                'safeName'     => 'sakura-wood-wall',
            ],
        ];

        return $this;
    }

}