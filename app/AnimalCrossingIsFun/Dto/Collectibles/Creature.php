<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

abstract class Creature extends Collectible {

    protected const IMAGE_PATH = 'creatures';

    /** @var string */
    protected $location;

    /** @var int */
    protected $price;

    /** @var string */
    protected $time;

    /** @var bool */
    protected $jan;

    /** @var bool */
    protected $feb;

    /** @var bool */
    protected $mar;

    /** @var bool */
    protected $apr;

    /** @var bool */
    protected $may;

    /** @var bool */
    protected $jun;

    /** @var bool */
    protected $jul;

    /** @var bool */
    protected $aug;

    /** @var bool */
    protected $sep;

    /** @var bool */
    protected $oct;

    /** @var bool */
    protected $nov;

    /** @var bool */
    protected $dec;

    /** @var int[] */
    protected $months;

    /**
     * @param array $properties
     */
    public function __construct(array $properties = []) {
        parent::__construct($properties);

        if(empty($properties)) {
            return;
        }
        if(isset($properties['jan'])) {
            $this->months = [
                $properties['jan'], $properties['feb'], $properties['mar'], $properties['apr'],
                $properties['may'], $properties['jun'], $properties['jul'], $properties['aug'],
                $properties['sep'], $properties['oct'], $properties['nov'], $properties['dec'],
            ];
        }
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getPrice(): int {
        return (int)$this->price;
    }

    /**
     * @return string[]
     */
    public function getTime(): array {
        return is_array($this->time) ? $this->time : explode(" & ", $this->time ?? '');
    }

    /**
     * @return bool
     */
    public function isJan(): bool {
        return $this->jan;
    }

    /**
     * @return bool
     */
    public function isFeb(): bool {
        return $this->feb;
    }

    /**
     * @return bool
     */
    public function isMar(): bool {
        return $this->mar;
    }

    /**
     * @return bool
     */
    public function isApr(): bool {
        return $this->apr;
    }

    /**
     * @return bool
     */
    public function isMay(): bool {
        return $this->may;
    }

    /**
     * @return bool
     */
    public function isJun(): bool {
        return $this->jun;
    }

    /**
     * @return bool
     */
    public function isJul(): bool {
        return $this->jul;
    }

    /**
     * @return bool
     */
    public function isAug(): bool {
        return $this->aug;
    }

    /**
     * @return bool
     */
    public function isSep(): bool {
        return $this->sep;
    }

    /**
     * @return bool
     */
    public function isOct(): bool {
        return $this->oct;
    }

    /**
     * @return bool
     */
    public function isNov(): bool {
        return $this->nov;
    }

    /**
     * @return bool
     */
    public function isDec(): bool {
        return $this->dec;
    }

    /**
     * @var string[]
     */
    protected $numToMonth = [
        0  => 'Jan',
        1  => 'Feb',
        2  => 'Mar',
        3  => 'Apr',
        4  => 'May',
        5  => 'Jun',
        6  => 'Jul',
        7  => 'Aug',
        8  => 'Sep',
        9  => 'Oct',
        10 => 'Nov',
        11 => 'Dec',
    ];

    /**
     * @var string[]
     */
    protected $numToMonthName = [
        0  => 'January',
        1  => 'February',
        2  => 'March',
        3  => 'April',
        4  => 'May',
        5  => 'June',
        6  => 'July',
        7  => 'August',
        8  => 'September',
        9  => 'October',
        10 => 'November',
        11 => 'December',
    ];

    /**
     * @var string[]
     */
    protected $numToMonthLetter = [
        0  => 'J',
        1  => 'F',
        2  => 'M',
        3  => 'A',
        4  => 'M',
        5  => 'J',
        6  => 'J',
        7  => 'A',
        8  => 'S',
        9  => 'O',
        10 => 'N',
        11 => 'D',
    ];

    /**
     * @return string
     */
    public function getPeriodOfYear() {
        $firstMonth = false;
        if(
            true === $this->months[0] &&
            true === $this->months[11]
        ) {
            for($i = count($this->months) - 1; $i >= 0; $i--) {
                if($this->months[$i] === false) {
                    $firstMonth = $i + 1;
                    break;
                }
            }
        }

        $lastMonth = 0;
        for($i = 0; $i < count($this->months); $i++) {
            if(true === $this->months[$i]) {
                if($firstMonth === false) {
                    $firstMonth = $i;
                }
                $lastMonth = $i;
                continue;
            }
            if(false === $this->months[$i]) {
                if(false !== $firstMonth) {
                    break;
                }
            }
        }

        return "{$this->numToMonth[$firstMonth]} - {$this->numToMonth[$lastMonth]}";
    }

    /**
     * @return bool
     */
    public function hasIrregularMonths() {
        switch(strtolower($this->name)) {
            // Fish
            case 'blue marlin':
            case 'char':
            case 'cherry salmon':
            case 'golden trout':
            // Bugs
            case 'emperor butterfly':
            case 'ladybug':
            case 'rajah brooke\'s birdwing':
            case 'violin beetle':
            case 'yellow butterfly':
                // @TODO: Case insensitivity
                return true;
        }

        return false;
    }

    public function getBullets(): array {
        $return = [];
        foreach($this->months as $index => $value) {
            $monthLetter = $this->numToMonth[$index];
            $return[] = [
                'month' => $monthLetter,
                'value' => $value,
            ];
        }

        return $return;
    }

}
