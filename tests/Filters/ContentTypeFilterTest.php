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
use GrahamCampbell\Exceptions\Displayers\JsonApiDisplayer;
use GrahamCampbell\Exceptions\Displayers\JsonDisplayer;
use GrahamCampbell\Exceptions\ExceptionInfo;
use GrahamCampbell\Exceptions\Filters\ContentTypeFilter;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery;

/**
 * This is the content type filter test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ContentTypeFilterTest extends AbstractTestCase
{
    public function testAcceptAll()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['*/*']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptHtmlAndAll()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/html', '*/*']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustHtml()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/html']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptText()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/*']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptJsonAndAll()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/json', '*/*']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustJson()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/json']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$json], $displayers);
    }

    public function testAcceptApplication()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));
        $api = new JsonApiDisplayer(new ExceptionInfo('bar'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/*']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json, $api], $exception, $exception, 500);

        $this->assertSame([$json, $api], $displayers);
    }

    public function testAcceptComplexJson()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));
        $api = new JsonApiDisplayer(new ExceptionInfo('bar'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/foo+json']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json, $api], $exception, $exception, 500);

        $this->assertSame([], $displayers);
    }

    public function testAcceptJsonApi()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $json = new JsonDisplayer(new ExceptionInfo('foo'));
        $api = new JsonApiDisplayer(new ExceptionInfo('bar'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/vnd.api+json']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $json, $api], $exception, $exception, 500);

        $this->assertSame([$api], $displayers);
    }

    public function testAcceptManyThings()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/*', 'application/foo+xml']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptNothing()
    {
        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer(new ExceptionInfo('foo'), 'foo');
        $json = new JsonDisplayer(new ExceptionInfo('foo'));

        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/xml']);

        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], $exception, $exception, 500);

        $this->assertSame([], $displayers);
    }
}
