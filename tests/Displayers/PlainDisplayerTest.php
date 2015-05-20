<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions\Displayers;

use Exception;
use GrahamCampbell\Exceptions\Displayers\PlainDisplayer;
use GrahamCampbell\Tests\Exceptions\AbstractTestCase;

/**
 * This is the plain displayer test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class PlainDisplayerTest extends AbstractTestCase
{
    public function testExceptionHandlerIsInjectable()
    {
        $actual = (new PlainDisplayer())->display(new Exception('Oh noes!'), 502);

        $expected = file_get_contents(__DIR__.'/stubs/plain.txt');

        $this->assertSame($expected, $actual);
    }
}
