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
use GrahamCampbell\Exceptions\Displayers\HtmlDisplayer;
use GrahamCampbell\Tests\Exceptions\AbstractTestCase;

/**
 * This is the html displayer test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class HtmlDisplayerTest extends AbstractTestCase
{
    public function testExceptionHandlerIsInjectable()
    {
        $actual = (new HtmlDisplayer())->display(new Exception('Oh noes!'), 502, [])->getContent();

        $expected = file_get_contents(__DIR__.'/stubs/html.txt');

        $this->assertSame($expected, $actual);
    }
}
