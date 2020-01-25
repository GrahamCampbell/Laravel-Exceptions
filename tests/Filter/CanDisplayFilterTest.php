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

namespace GrahamCampbell\Tests\Exceptions\Filter;

use Exception;
use GrahamCampbell\Exceptions\Displayer\DisplayerInterface;
use GrahamCampbell\Exceptions\Filter\CanDisplayFilter;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery;

/**
 * This is the can display filter test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CanDisplayFilterTest extends AbstractTestCase
{
    public function testFirstIsRemoved()
    {
        $request = Mockery::mock(Request::class);
        $exception = new Exception();
        $html = Mockery::mock(DisplayerInterface::class);
        $html->shouldReceive('canDisplay')->once()->with($exception, $exception, 500)->andReturn(false);
        $json = Mockery::mock(DisplayerInterface::class);
        $json->shouldReceive('canDisplay')->once()->with($exception, $exception, 500)->andReturn(true);

        $displayers = (new CanDisplayFilter())->filter([$html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$json], $displayers);
    }

    public function testNoChange()
    {
        $request = Mockery::mock(Request::class);
        $exception = new Exception();
        $html = Mockery::mock(DisplayerInterface::class);
        $html->shouldReceive('canDisplay')->once()->with($exception, $exception, 500)->andReturn(true);
        $json = Mockery::mock(DisplayerInterface::class);
        $json->shouldReceive('canDisplay')->once()->with($exception, $exception, 500)->andReturn(true);

        $displayers = (new CanDisplayFilter())->filter([$html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$html, $json], $displayers);
    }
}
