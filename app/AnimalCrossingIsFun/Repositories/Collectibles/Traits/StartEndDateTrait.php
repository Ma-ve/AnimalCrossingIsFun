<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles\Traits;

use DateTime;
use Exception;

trait StartEndDateTrait {

    /**
     * @param array $data
     *
     * @return array
     */
    protected function transformDates(array $data) {
        return array_map(function($item) {
            if(isset($item['startDate']) && $item['startDate']) {
                if(!($item['startDate'] instanceof DateTime)) {
                    $item['startDate'] = DateTime::createFromFormat('m-d', $item['startDate']);
                    if($this->hasMonthPassed($item['startDate'])) {
                        $item['startDate']->modify('+1 year');
                    }
                }
            }
            if(isset($item['endDate']) && $item['endDate']) {
                if(!($item['endDate'] instanceof DateTime)) {
                    $item['endDate'] = DateTime::createFromFormat('m-d', $item['endDate']);
                    if($this->hasMonthPassed($item['endDate'])) {
                        $item['endDate']->modify('+1 year');
                    }
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
        }, $data);
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