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
use GrahamCampbell\Exceptions\Displayers\DebugDisplayer;
use GrahamCampbell\Exceptions\Displayers\HtmlDisplayer;
use GrahamCampbell\Exceptions\Displayers\JsonDisplayer;
use GrahamCampbell\Exceptions\Filters\ContentTypeFilter;
use GrahamCampbell\TestBench\AbstractTestCase;
use Mockery;

/**
 * This is the content type filter test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class ContentTypeFilterTest extends AbstractTestCase
{
    public function testAcceptAll()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['*/*']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptHtmlAndAll()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['text/html', '*/*']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustHtml()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['text/html']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptText()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['text/*']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptJsonAndAll()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['application/json', '*/*']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustJson()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['application/json']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$json], $displayers);
    }

    public function testAcceptApplication()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['application/*']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$json], $displayers);
    }

    public function testAcceptComplexJson()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['application/foo+json']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$json], $displayers);
    }

    public function testAcceptManyThings()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['text/*', 'application/foo+xml']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([$debug, $html], $displayers);
    }

    public function testAcceptNothing()
    {
        $debug = new DebugDisplayer();
        $html = new HtmlDisplayer('foo');
        $json = new JsonDisplayer();

        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('getAcceptableContentTypes')->once()->andReturn(['application/xml']);


        $displayers = (new ContentTypeFilter($request))->filter([$debug, $html, $json], new Exception());

        $this->assertSame([], $displayers);
    }
}
