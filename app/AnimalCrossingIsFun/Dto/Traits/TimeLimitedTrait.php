<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Traits;

use DateTime;

trait TimeLimitedTrait {

    /**
     * @var string|DateTime
     */
    protected $startDate;

    /**
     * @var string|DateTime
     */
    protected $endDate;

    /**
     * @return string|DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @return string|DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @return DateTime|string
     */
    public function getFullDateRange() {
        return $this->getDateRange($formatWithoutYear = 'F jS', $formatWithYear = 'F jS, Y');
    }

    /**
     * @return DateTime|string
     */
    public function getShortDateRange() {
        return $this->getDateRange($formatWithoutYear = 'M jS', $formatWithYear = 'M jS');
    }

    /**
     * @param string $formatWithoutYear
     * @param string $formatWithYear
     *
     * @return string
     */
    private function getDateRange($formatWithoutYear = 'F jS', $formatWithYear = 'F jS, Y') {
        if(is_string($this->startDate)) {
            return $this->startDate;
        }

        if($this->startDate->format('Y-m-d') === $this->endDate->format('Y-m-d')) {
            return $this->startDate->format($formatWithYear);
        }

        $formattedStartDate = $this->startDate->format($formatWithoutYear);
        $formattedEndDate = $this->endDate->format($formatWithYear);

        return "{$formattedStartDate} - {$formattedEndDate}";
    }


}