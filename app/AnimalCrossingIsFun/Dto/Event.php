<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto;

use DateTime;

class Event extends Dto {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string|DateTime
     */
    protected $startDate;

    /**
     * @var string|DateTime
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $hemisphere;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $detailsLink;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string {
        return $this->icon;
    }

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
     * @return string
     */
    public function getHemisphere(): string {
        return $this->hemisphere;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDetailsLink(): string {
        return $this->detailsLink;
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
