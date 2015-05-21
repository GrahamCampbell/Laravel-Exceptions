<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions\Filters;

use Exception;
use GrahamCampbell\Exceptions\Filters\CanDisplayFilter;
use GrahamCampbell\TestBench\AbstractTestCase;
use Mockery;

/**
 * This is the can display filter test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class CanDisplayFilterTest extends AbstractTestCase
{
    public function testFirstIsRemoved()
    {
        $exception = new Exception();
        $html = Mockery::mock('GrahamCampbell\Exceptions\Displayers\HtmlDisplayer');
        $html->shouldReceive('canDisplay')->once()->with($exception)->andReturn(false);
        $json = Mockery::mock('GrahamCampbell\Exceptions\Displayers\JsonDisplayer');
        $json->shouldReceive('canDisplay')->once()->with($exception)->andReturn(true);

        $displayers = (new CanDisplayFilter())->filter([$html, $json], $exception);

        $this->assertSame([$json], $displayers);
    }

    public function testNoChange()
    {
        $exception = new Exception();
        $html = Mockery::mock('GrahamCampbell\Exceptions\Displayers\HtmlDisplayer');
        $html->shouldReceive('canDisplay')->once()->with($exception)->andReturn(true);
        $json = Mockery::mock('GrahamCampbell\Exceptions\Displayers\JsonDisplayer');
        $json->shouldReceive('canDisplay')->once()->with($exception)->andReturn(true);

        $displayers = (new CanDisplayFilter())->filter([$html, $json], $exception);

        $this->assertSame([$html, $json], $displayers);
    }
}
