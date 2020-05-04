<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use DateTime;
use Exception;
use Mave\AnimalCrossingIsFun\Dto\Dto;
use Mave\AnimalCrossingIsFun\Dto\Event as EventDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BaseRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Traits\StartEndDateTrait;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;
use Mave\AnimalCrossingIsFun\Services\DateService;

/**
 * @method getAll()
 */
class EventRepository extends BaseRepository implements IRepository {
    use StartEndDateTrait;

    /**
     * @var string
     */
    protected $dto = EventDto::class;

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
     * @param DateTime $dt
     *
     * @return Dto[]|EventDto[]
     */
    public function getByNearbyStartDates(DateTime $dt) {
        $this->contents = array_filter($this->contents, function($event) use ($dt) {
            /** @var EventDto $event */
            if(is_string($event['startDate'])) {
                return false;
            }

            $diff = $event['startDate']
                ->diff($dt);


            if($diff->invert) {
                return $diff->days < 30;
            }
            $diffBetweenStartAndEnd = $event['startDate']->diff($event['endDate'])->days;

            if($diff->days <= $diffBetweenStartAndEnd) {
                return true;
            }

            return false;
        });

        return parent::map(new $this->dto);
    }

    /**
     * @param bool|string $sort
     *
     * @return $this
     */
    public function sortItems($sort = false) {
        switch($sort) {
            default:
                usort($this->contents, function($a, $b) {
                    return $a['startDate'] <=> $b['startDate'];
                });
                break;
            case 'name':
                $this->sortByNameAsc();
                break;
            case '-name':
                $this->sortByNameDesc();
                break;
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $mapped = $this->transformDates($this->databaseService->loadFromDatabase('events'));

        $this->contents = $mapped;

        return $this;
    }

}