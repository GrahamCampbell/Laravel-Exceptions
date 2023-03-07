<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
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
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\Exceptions\Information\NullInformation;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery;

/**
 * This is the content type filter test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ContentTypeFilterTest extends AbstractTestCase
{
    public function testAcceptAll(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['*/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptHtmlAndAll(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/html', '*/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustHtml(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/html']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$debug, $html], $displayers);
    }

    public function testAcceptText(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$debug, $html], $displayers);
    }

    public function testAcceptJsonAndAll(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/json', '*/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$debug, $html, $json], $displayers);
    }

    public function testAcceptJustJson(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/json']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$json], $displayers);
    }

    public function testAcceptApplication(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/*']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));
        $api = new JsonApiDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json, $api], $request, $exception, $exception, 500);

        self::assertSame([$json, $api], $displayers);
    }

    public function testAcceptComplexJson(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/foo+json']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));
        $api = new JsonApiDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json, $api], $request, $exception, $exception, 500);

        self::assertSame([], $displayers);
    }

    public function testAcceptJsonApi(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/vnd.api+json']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));
        $api = new JsonApiDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $json, $api], $request, $exception, $exception, 500);

        self::assertSame([$api], $displayers);
    }

    public function testAcceptManyThings(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['text/*', 'application/foo+xml']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([$debug, $html], $displayers);
    }

    public function testAcceptNothing(): void
    {
        $request = Mockery::mock(Request::class)->makePartial();
        $request->shouldReceive('getAcceptableContentTypes')->andReturn(['application/xml']);

        $exception = new Exception();
        $debug = new DebugDisplayer();
        $html = self::getHtmlDisplayer();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new ContentTypeFilter())->filter([$debug, $html, $json], $request, $exception, $exception, 500);

        self::assertSame([], $displayers);
    }

    private static function getHtmlDisplayer(): HtmlDisplayer
    {
        $assets = static function (string $path): string {
            return 'https://example.com/'.ltrim($path, '/');
        };

        return new HtmlDisplayer(new NullInformation(new InformationMerger()), $assets, 'foo');
    }
}
