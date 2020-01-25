<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
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
 * @author Graham Campbell <graham@alt-three.com>
 */
class HashingIdentifierTest extends AbstractTestCase
{
    public function testIdentifyOne()
    {
        $i = new HashingIdentifier();

        $e = new Exception();

        $this->assertSame($i->identify($e), $i->identify($e));
    }

    public function testIdentifyTwo()
    {
        $i = new HashingIdentifier();

        $first = new Exception();
        $second = new Exception();

        $this->assertSame($i->identify($first), $i->identify($first));
        $this->assertSame($i->identify($second), $i->identify($second));
        $this->assertNotSame($i->identify($first), $i->identify($second));
    }

    public function testIdentifyMany()
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
        $this->assertNotSame($i->identify($arr[0]), $ids[0]);
        $this->assertNotSame($i->identify($arr[2]), $ids[2]);
        $this->assertNotSame($i->identify($arr[5]), $ids[5]);

        // these should still be in memory
        $this->assertSame($i->identify($arr[7]), $ids[7]);
        $this->assertSame($i->identify($arr[15]), $ids[15]);
    }
}
