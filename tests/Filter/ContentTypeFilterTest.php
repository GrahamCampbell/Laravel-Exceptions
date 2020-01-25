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
use GrahamCampbell\Exceptions\Displayer\DebugDisplayer;
use GrahamCampbell\Exceptions\Displayer\HtmlDisplayer;
use GrahamCampbell\Exceptions\Displayer\JsonApiDisplayer;
use GrahamCampbell\Exceptions\Displayer\JsonDisplayer;
use GrahamCampbell\Exceptions\Filter\ContentTypeFilter;
use GrahamCampbell\Exceptions\Information\NullInformation;
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
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['*/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptHtmlAndAll()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/html', '*/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustHtml()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/html']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptText()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptJsonAndAll()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/json', '*/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustJson()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/json']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$json], $displayers);
    }

    public function testAcceptApplication()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());
        $api = new JsonApiDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json, $api], $request, $exception, $exception, 500);

        $this->assertSame([$json, $api], $displayers);
    }

    public function testAcceptComplexJson()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/foo+json']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());
        $api = new JsonApiDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json, $api], $request, $exception, $exception, 500);

        $this->assertSame([], $displayers);
    }

    public function testAcceptJsonApi()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/vnd.api+json']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $json = new JsonDisplayer(new NullInformation());
        $api = new JsonApiDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $json, $api], $request, $exception, $exception, 500);

        $this->assertSame([$api], $displayers);
    }

    public function testAcceptManyThings()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/*', 'application/foo+xml']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptNothing()
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/xml']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = $this->getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation());

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        $this->assertSame([], $displayers);
    }

    protected function getHtmlDisplayer()
    {
        $assets = function ($path) {
            return 'https://example.com/'.ltrim($path, '/');
        };

        return new HtmlDisplayer(new NullInformation(), $assets, 'foo');
    }
}
