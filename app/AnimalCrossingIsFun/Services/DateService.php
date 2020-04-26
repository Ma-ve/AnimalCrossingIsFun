<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Services;

use DateTime;

class DateService {

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DateTime|null $dateTime
     */
    public function __construct(?DateTime $dateTime = null) {
        $this->dateTime = $dateTime ?? new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getDateTime() {
        return $this->dateTime;
    }

    /**
     * @param int $year
     *
     * @return DateTime
     */
    public function getEasterDateTime(?int $year = null) {
        if(null === $year) {
            $year = (int)date('Y');
        }
        $base = new DateTime("{$year}-03-21");
        $days = easter_days($year);

        return $base->modify("+{$days} days");
    }

}
