<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

use Mave\AnimalCrossingIsFun\Dto\Traits\TimeLimitedTrait;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\CherryBlossomRecipeRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Recipes\YoungSpringBambooRepository;

class RecipeCategory extends Collectible {
    use TimeLimitedTrait;

    protected const IMAGE_PATH = 'recipe-categories';

    /** @var string */
    protected $eventName;

    /** @var string */
    protected $hemisphere;

    /** @var string */
    protected $recipeRepository;

    /** @var string */
    protected $group = 'recipe-category';

    /**
     * @return string
     */
    public function getEventName(): string {
        return $this->eventName;
    }

    /**
     * @return string
     */
    public function getHemisphere(): string {
        return $this->hemisphere;
    }

    /**
     * @return string
     */
    public function getGroup(): string {
        return $this->group;
    }

    /**
     * @return CherryBlossomRecipeRepository|YoungSpringBambooRepository
     */
    public function getRecipeRepository() {
        return new $this->recipeRepository(null);
    }

}
