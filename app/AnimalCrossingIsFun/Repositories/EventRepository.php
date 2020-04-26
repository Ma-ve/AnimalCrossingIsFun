<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use DateTime;
use Exception;
use Mave\AnimalCrossingIsFun\Dto\Dto;
use Mave\AnimalCrossingIsFun\Dto\Event as EventDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BaseRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;
use Mave\AnimalCrossingIsFun\Repositories\Services\Interfaces\IDatabaseService;
use Mave\AnimalCrossingIsFun\Services\DateService;

/**
 * @method getAll()
 */
class EventRepository extends BaseRepository implements IRepository {

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
     * @var string
     */
    protected $dto = EventDto::class;

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
        usort($this->contents, function($a, $b) {
            return $a['startDate'] <=> $b['startDate'];
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
        $this->contents = array_map(function($item) {
            if(isset($item['startDate']) && $item['startDate']) {
                $item['startDate'] = DateTime::createFromFormat('m-d', $item['startDate']);
                if($this->hasMonthPassed($item['startDate'])) {
                    $item['startDate']->modify('+1 year');
                }
            }
            if(isset($item['endDate']) && $item['endDate']) {
                $item['endDate'] = DateTime::createFromFormat('m-d', $item['endDate']);
                if($this->hasMonthPassed($item['endDate'])) {
                    $item['endDate']->modify('+1 year');
                }
            }

            if(false !== $item['startDate'] && false === $item['endDate']) {
                if(!isset($item['endDateTimeFunction'])) {
                    throw new Exception('Expected endDateTimeFunction');
                }

                if(method_exists($this->dateService, ($item['endDateTimeFunction']))) {
                    $year = (int)$this->dateService->getDateTime()->format('Y');

                    if($this->hasMonthPassed($item['startDate'])) {
                        $year++;
                    }
                    $item['endDate'] = $this->dateService->{$item['endDateTimeFunction']}($year);
                }
            }
            if(false === $item['startDate']) {
                if(isset($item['dayData'])) {
                    $timeString = "{$item['dayData']} of {$item['monthData']}";
                    try {
                        $dateTime = (new DateTime($timeString));
                    } catch(Exception $exception) {
                        $item['endDate'] =
                        $item['startDate'] = "{$item['dayData']} of {$item['monthData']}";

                        return $item;
                    }

                    $hasMonthPassed = DateTime::createFromFormat('F', $item['monthData']);
                    if($this->hasMonthPassed($hasMonthPassed)) {
                        $dateTime = (new DateTime('+1 year'))->modify($timeString);
                    }

                    $item['endDate'] =
                    $item['startDate'] = $dateTime;
                }
            }

            return $item;
        }, $this->databaseService->loadFromDatabase('events'));
        

        return $this;
    }

    /**
     * @param DateTime $dateTime
     *
     * @return bool
     */
    private function hasMonthPassed(DateTime $dateTime): bool {
        return (int)$dateTime->format('m') < (int)($this->dateService->getDateTime())->format('m');
    }

}