<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Tests\Repositories;

use DateTime;
use Mave\AnimalCrossingIsFun\Repositories\EventRepository;
use Mave\AnimalCrossingIsFun\Services\DateService;
use Mave\AnimalCrossingIsFun\Tests\MockDatabaseService;
use PHPUnit\Framework\TestCase;

class EventRepositoryTest extends TestCase {

    public function testStaticStartAndEndDate() {
        $data = [
            // Single day event
            [
                'expectedFullDateRange'  => 'January 1st, ' . date('Y'),
                'expectedShortDateRange' => 'Jan 1st',
                'dateTime'               => new DateTime('2020-01-01'),
                'attributes'             => [
                    'startDate' => '01-01',
                    'endDate'   => '01-01',
                ],
            ],
            [
                'expectedFullDateRange'  => 'January 1st, ' . date('Y'),
                'expectedShortDateRange' => 'Jan 1st',
                'dateTime'               => new DateTime('2020-01-31'),
                'attributes'             => [
                    'startDate' => '01-01',
                    'endDate'   => '01-01',
                ],
            ],
            [
                'expectedFullDateRange'  => 'January 1st, ' . (date('Y') + 1),
                'expectedShortDateRange' => 'Jan 1st',
                'dateTime'               => new DateTime('2020-02-01'),
                'attributes'             => [
                    'startDate' => '01-01',
                    'endDate'   => '01-01',
                ],
            ],

            // Start and end date within a couple of days
            [
                'expectedFullDateRange'  => 'January 1st - January 7th, ' . date('Y'),
                'expectedShortDateRange' => 'Jan 1st - Jan 7th',
                'dateTime'               => new DateTime('2020-01-01'),
                'attributes'             => [
                    'startDate' => '01-01',
                    'endDate'   => '01-07',
                ],
            ],
            [
                'expectedFullDateRange'  => 'January 1st - January 7th, ' . (date('Y') + 1),
                'expectedShortDateRange' => 'Jan 1st - Jan 7th',
                'dateTime'               => new DateTime('2020-02-01'),
                'attributes'             => [
                    'startDate' => '01-01',
                    'endDate'   => '01-07',
                ],
            ],

            // Start and end date spanning multiple months
            [
                'expectedFullDateRange'  => 'May 4th - June 21st, ' . date('Y'),
                'expectedShortDateRange' => 'May 4th - Jun 21st',
                'dateTime'               => new DateTime('2020-02-01'),
                'attributes'             => [
                    'startDate' => '05-04',
                    'endDate'   => '06-21',
                ],
            ],
        ];

        $this->assertData($data);
    }

    public function testNoStartDate() {
        self::markTestIncomplete('Date parsing does not properly work');
        $data = [
            [
                'expectedFullDateRange'  => 'January 11th, 2020',
                'expectedShortDateRange' => 'Jan 11th',
                'dateTime'               => new DateTime('2020-01-01'),
                'attributes'             => [
                    'startDate' => false,
                    'endDate'   => false,
                    'monthData' => 'January',
                    'dayData'   => 'Second Saturday',
                ],
            ],
            [
                'expectedFullDateRange'  => 'January 9th, ' . (date('Y') + 1),
                'expectedShortDateRange' => 'Jan 9th',
                'dateTime'               => new DateTime('2020-06-01'),
                'attributes'             => [
                    'startDate' => false,
                    'endDate'   => false,
                    'monthData' => 'January',
                    'dayData'   => 'Second Saturday',
                ],
            ],

            [
                'expectedFullDateRange'  => 'Varies of January or February',
                'expectedShortDateRange' => 'Varies of January or February',
                'dateTime'               => new DateTime('2020-06-01'),
                'attributes'             => [
                    'startDate' => false,
                    'endDate'   => false,
                    'monthData' => 'January or February',
                    'dayData'   => 'Varies',
                ],
            ],

            [
                'expectedFullDateRange'  => 'April 1st - April 12th, ' . date('Y'),
                'expectedShortDateRange' => 'Apr 1st - Apr 12th',
                'dateTime'               => new DateTime('2020-04-01'),
                'attributes'             => [
                    'startDate'           => '04-01',
                    'endDate'             => false,
                    'endDateTimeFunction' => 'getEasterDateTime',
                ],
            ],
            [
                'expectedFullDateRange'  => 'April 1st - April 4th, ' . (date('Y') + 1),
                'expectedShortDateRange' => 'Apr 1st - Apr 4th',
                'dateTime'               => new DateTime('2020-06-01'),
                'attributes'             => [
                    'startDate'           => '04-01',
                    'endDate'             => false,
                    'endDateTimeFunction' => 'getEasterDateTime',
                ],
            ],
//            [
//                'expectedFullDateRange'  => 'April 1st - April 17th, 2022',
//                'expectedShortDateRange' => 'Apr 1st - Apr 17th',
//                'dateTime'               => new DateTime('2021-12-31'),
//                'attributes'             => [
//                    'startDate'           => '04-01',
//                    'endDate'             => false,
//                    'endDateTimeFunction' => 'getEasterDateTime',
//                ],
//            ],
        ];

        $this->assertData($data);
    }

    /**
     * @param array $data
     *
     * @throws \Exception
     */
    private function assertData(array $data) {
        foreach($data as $assertData) {
            $mockDatabaseService = new MockDatabaseService([$assertData['attributes']]);
            $dateService = new DateService($assertData['dateTime']);
            $dtoList = (new EventRepository($mockDatabaseService, $dateService))
                ->loadAll()
                ->getAll();
            self::assertCount(1, $dtoList);
            self::assertEquals($assertData['expectedFullDateRange'], $dtoList[0]->getFullDateRange());
            self::assertEquals($assertData['expectedShortDateRange'], $dtoList[0]->getShortDateRange());
        }
    }

}
