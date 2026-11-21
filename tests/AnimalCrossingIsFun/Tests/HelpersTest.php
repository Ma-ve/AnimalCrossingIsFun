<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Tests;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase {

    protected function tearDown(): void {
        unset($_ENV['ACIF_TEST_KEY'], $_SERVER['ACIF_TEST_KEY']);
        putenv('ACIF_TEST_KEY');
    }

    public function testEnvReadsValuesThatAreOnlyInEnvSuperglobal(): void {
        // Dotenv only fills $_ENV/$_SERVER when putenv() is disabled
        $_ENV['ACIF_TEST_KEY'] = 'secret';

        self::assertSame('secret', env('ACIF_TEST_KEY'));
    }

    public function testEnvReadsValuesThatAreOnlyInServerSuperglobal(): void {
        $_SERVER['ACIF_TEST_KEY'] = 'secret';

        self::assertSame('secret', env('ACIF_TEST_KEY'));
    }

    public function testEnvFallsBackToGetenv(): void {
        putenv('ACIF_TEST_KEY=secret');

        self::assertSame('secret', env('ACIF_TEST_KEY'));
    }

    public function testEnvParsesSpecialValues(): void {
        $_ENV['ACIF_TEST_KEY'] = 'false';
        self::assertFalse(env('ACIF_TEST_KEY'));

        $_ENV['ACIF_TEST_KEY'] = '(null)';
        self::assertNull(env('ACIF_TEST_KEY', 'default'));
    }

    public function testEnvReturnsDefaultWhenMissing(): void {
        self::assertSame('default', env('ACIF_TEST_KEY', 'default'));
    }

}
