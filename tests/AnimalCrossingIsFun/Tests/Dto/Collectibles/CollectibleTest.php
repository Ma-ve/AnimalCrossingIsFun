<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Dto\Collectibles;

use Exception;
use Mave\AnimalCrossingIsFun\Dto\Dto;
use PHPUnit\Framework\TestCase;

class CollectibleTest extends TestCase {

    public function testSafeName() {
        $data = [
            [
                'input'    => 'K.K. Étude',
                'expected' => 'k-k-tude',
            ],
            [
                'input'    => '  K.K. Étude  ',
                'expected' => 'k-k-tude',
            ],
            [
                'input'    => 'Tango K.K.',
                'expected' => 'tango-k-k',
            ],
            [
                'input'    => 'Tango €K.K...._--',
                'expected' => 'tango-k-k',
            ],
        ];

        foreach($data as $input) {
            self::assertEquals($input['expected'], Collectible::getSafeNameForString($input['input']));
        }
    }
}
