<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions\Identifier;

use Exception;
use GrahamCampbell\Exceptions\Identifier\HashingIdentifier;
use GrahamCampbell\TestBench\AbstractTestCase;

/**
 * This is the hashing identifier test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class HashingIdentifierTest extends AbstractTestCase
{
    public function testIdentifyOne(): void
    {
        $i = new HashingIdentifier();

        $e = new Exception();

        self::assertSame($i->identify($e), $i->identify($e));
    }

    public function testIdentifyTwo(): void
    {
        $i = new HashingIdentifier();

        $first = new Exception();
        $second = new Exception();

        self::assertSame($i->identify($first), $i->identify($first));
        self::assertSame($i->identify($second), $i->identify($second));
        self::assertNotSame($i->identify($first), $i->identify($second));
    }

    public function testIdentifyMany(): void
    {
        $i = new HashingIdentifier();

        $arr = [];
        for ($j = 0; $j < 36; $j++) {
            $arr[] = new Exception();
        }

        $ids = [];
        foreach ($arr as $e) {
            $ids[] = $i->identify($e);
        }

        // these should have been flushed
        self::assertNotSame($i->identify($arr[0]), $ids[0]);
        self::assertNotSame($i->identify($arr[2]), $ids[2]);
        self::assertNotSame($i->identify($arr[5]), $ids[5]);

        // these should still be in memory
        self::assertSame($i->identify($arr[7]), $ids[7]);
        self::assertSame($i->identify($arr[15]), $ids[15]);
    }
}
