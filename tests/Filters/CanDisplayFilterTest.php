<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions\Filters;

use Exception;
use GrahamCampbell\Exceptions\Displayers\HtmlDisplayer;
use GrahamCampbell\Exceptions\Displayers\JsonDisplayer;
use GrahamCampbell\Exceptions\Filters\CanDisplayFilter;
use GrahamCampbell\TestBench\AbstractTestCase;
use Mockery;

/**
 * This is the can display filter test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class CanDisplayFilterTest extends AbstractTestCase
{
    public function testFirstIsRemoved()
    {
        $exception = new Exception();
        $html = Mockery::mock(HtmlDisplayer::class);
        $html->shouldReceive('canDisplay')->once()->with($exception)->andReturn(false);
        $json = Mockery::mock(JsonDisplayer::class);
        $json->shouldReceive('canDisplay')->once()->with($exception)->andReturn(true);

        $displayers = (new CanDisplayFilter())->filter([$html, $json], $exception);

        $this->assertSame([$json], $displayers);
    }

    public function testNoChange()
    {
        $exception = new Exception();
        $html = Mockery::mock(HtmlDisplayer::class);
        $html->shouldReceive('canDisplay')->once()->with($exception)->andReturn(true);
        $json = Mockery::mock(JsonDisplayer::class);
        $json->shouldReceive('canDisplay')->once()->with($exception)->andReturn(true);

        $displayers = (new CanDisplayFilter())->filter([$html, $json], $exception);

        $this->assertSame([$html, $json], $displayers);
    }
}
