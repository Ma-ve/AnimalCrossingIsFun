<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

class DeepSeaCreature extends Creature {

    /** @var int */
    protected $id;

    /** @var string */
    protected $wiki_url;
    protected $icon_url;
    protected $image_url;
    protected $shadow_size;
    protected $swimming_pattern;

    /** @var string */
    protected $location;

    /** @var int */
    protected $shadowSize;

    /** @var string */
    protected $group = 'deep-sea-creatures';

    public function __construct(array $properties = []) {
        parent::__construct($properties);
        // @TODO: need to fix weird months => northern = [1, 5] format
        $this->months = array_map(function(int $i) {
            return $i - 1;
        }, $this->months['northern'] ?? []);

        foreach($this->months as $month) {
            $monthName = $this->numToMonth[$month] ?? false;
            if(false === $monthName) {
                continue;
            }
            $lowerMonthName = strtolower($monthName);
            if(property_exists($this, $lowerMonthName)) {
                $this->{$lowerMonthName} = true;
                if(!empty($this->numToMonthName[$month])) {
                    $this->filters[] = $this->numToMonthName[$month];
                }
            }
        }
        $newMonths = [];
        for($i = 0; $i < 12; $i++) {
            $newMonths[$i] = in_array($i, $this->months, true);
            $lowerMonthName = strtolower($this->numToMonth[$i]);
            if(property_exists($this, $lowerMonthName)) {
                $this->{$lowerMonthName} = true;
            }
        }
        $this->months = $newMonths;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location ?? ($this->swimming_pattern ?? '');
    }

    /**
     * @return int
     */
    public function getShadowSize(): int {
        return (int)$this->shadow_size;
    }

    public function getSwimmingPattern(): string {
        return $this->swimming_pattern;
    }

//    public function hasIrregularMonths() {
//        return true; // @TODO: need to fix weird months => northern = [1, 5] format
//    }

//    /**
//     * @return string
//     */
//    public function getPeriodOfYear() {
//        $firstMonth = false;
//
//        if(
//            in_array(1, $this->months, true) &&
//            in_array(12, $this->months, true)
//        ) {
//            for($i = 11; $i >= 0; $i--) {
//                if(!in_array($i + 1, $this->months, true)) {
//                    $firstMonth = $i;
//                    break;
//                }
//            }
//        }
//
//        $lastMonth = 0;
//        for($i = 0; $i < 12; $i++) {
//            if(in_array($i + 1, $this->months, true)) {
//                if($firstMonth === false) {
//                    $firstMonth = $i;
//                }
//                $lastMonth = $i;
//                continue;
//            }
//            if(!in_array($i + 1, $this->months, true)) {
//                if(false !== $firstMonth) {
//                    break;
//                }
//            }
//        }
//
//        return "{$this->numToMonth[$firstMonth]} - {$this->numToMonth[$lastMonth]}";
//    }

//    public function getBullets(): array {
//        $return = [];
//        for($i = 0; $i <= 11; $i++) {
//            $monthLetter = $this->numToMonth[$i];
//            $return[] = [
//                'month' => $monthLetter,
//                'value' => in_array($i, $this->months, true),
//            ];
//        }
//
//        return $return;
//    }

}
