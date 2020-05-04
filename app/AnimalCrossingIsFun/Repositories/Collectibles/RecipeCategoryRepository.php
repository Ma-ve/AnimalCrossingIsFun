<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Collectibles\RecipeCategory as RecipeCategoryDto;
use Mave\AnimalCrossingIsFun\Dto\Event as EventDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Traits\StartEndDateTrait;
use Mave\AnimalCrossingIsFun\Repositories\EventRepository;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;
use Mave\AnimalCrossingIsFun\Services\DateService;

class RecipeCategoryRepository extends BaseRepository implements IRepository {
    use StartEndDateTrait;

    /**
     * @var string
     */
    protected $dto = RecipeCategoryDto::class;

    /**
     * @var DateService
     */
    private $dateService;

    /**
     * @param IDatabaseService|null $databaseService
     * @param DateService|null      $dateService
     */
    public function __construct(?IDatabaseService $databaseService, ?DateService $dateService = null) {
        parent::__construct($databaseService);
        $this->dateService = $dateService ?? new DateService();
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $eventRepository = (new EventRepository(null))
            ->loadAll();

        /** @var EventDto[] $cherryBlossomEvents */
        $cherryBlossomEvents = $eventRepository->getMultipleBySingleKey('cherry-blossom-season');
        foreach($cherryBlossomEvents as $cherryBlossomEvent) {
            $this->contents[] = [
                'name'       => $cherryBlossomEvent->getName(),
                'eventName'  => $cherryBlossomEvent->getName(),
                'hemisphere' => $cherryBlossomEvent->getHemisphere(),
                'startDate'  => $cherryBlossomEvent->getStartDate(),
                'endDate'    => $cherryBlossomEvent->getEndDate(),
            ];
        }

        $this->contents = array_merge($this->contents, [
            [
                'name'       => 'Young Spring Bamboo',
                'hemisphere' => 'Northern',
                'startDate'  => '03-01',
                'endDate'    => '05-31',
            ],
            [
                'name'       => 'Young Spring Bamboo',
                'hemisphere' => 'Southern',
                'startDate'  => '09-01',
                'endDate'    => '11-30',
            ],
//            [
//                'name'       => 'Maple Leaf',
//                'hemisphere' => 'Southern',
//                'startDate'  => '05-16',
//                'endDate'    => '05-26',
//            ],
//            [
//                'name'       => 'Tree\'s Bounty',
//                'hemisphere' => 'Southern',
//                'startDate'  => '03-01',
//                'endDate'    => '06-11',
//            ],
//            [
//                'name'       => 'Tree\'s Bounty',
//                'hemisphere' => 'Northern',
//                'startDate'  => '09-01',
//                'endDate'    => '12-11',
//            ],
//            [
//                'name'       => 'Mushrooming Season',
//                'hemisphere' => 'Southern',
//                'startDate'  => '05-01',
//                'endDate'    => '06-01',
//            ],
        ]);

        $this->contents = $this->transformDates($this->contents);

        usort($this->contents, function($a, $b) {
            return $a['startDate'] <=> $b['startDate'];
        });

        return $this;
    }

}