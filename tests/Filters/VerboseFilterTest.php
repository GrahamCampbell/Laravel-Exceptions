<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions\Filters;

use Exception;
use GrahamCampbell\Exceptions\Displayers\DebugDisplayer;
use GrahamCampbell\Exceptions\Displayers\HtmlDisplayer;
use GrahamCampbell\Exceptions\Displayers\JsonDisplayer;
use GrahamCampbell\Exceptions\ExceptionInfo;
use GrahamCampbell\Exceptions\Filters\VerboseFilter;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Contracts\Config\Repository;
use Mockery;

/**
 * This is the verbose filter test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class VerboseFilterTest extends AbstractTestCase
{
    public function testDebugStaysOnTop()
    {
        $exception = new Exception();
        $verbose = new DebugDisplayer();
        $standard = new JsonDisplayer(new ExceptionInfo('foo'));

        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(true);

        $displayers = (new VerboseFilter($config))->filter([$verbose, $standard], $exception, $exception, 500);

        $this->assertSame([$verbose, $standard], $displayers);
    }

    public function testDebugIsRemoved()
    {
        $exception = new Exception();
        $verbose = new DebugDisplayer();
        $standard = new JsonDisplayer(new ExceptionInfo('foo'));

        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(false);

        $displayers = (new VerboseFilter($config))->filter([$verbose, $standard], $exception, $exception, 500);

        $this->assertSame([$standard], $displayers);
    }

    public function testNoChangeInDebugMode()
    {
        $exception = new Exception();
        $json = new JsonDisplayer(new ExceptionInfo('foo'));
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');

        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(true);

        $displayers = (new VerboseFilter($config))->filter([$json, $html], $exception, $exception, 500);

        $this->assertSame([$json, $html], $displayers);
    }

    public function testNoChangeNotInDebugMode()
    {
        $exception = new Exception();
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('get')->once()->with('app.debug', false)->andReturn(false);

        $displayers = (new VerboseFilter($config))->filter([$json], $exception, $exception, 500);

        $this->assertSame([$json], $displayers);
    }
}
