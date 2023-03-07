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
use GrahamCampbell\Exceptions\Displayer\JsonDisplayer;
use GrahamCampbell\Exceptions\Filter\VerboseFilter;
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\Exceptions\Information\NullInformation;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery;

/**
 * This is the verbose filter test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class VerboseFilterTest extends AbstractTestCase
{
    public function testDebugStaysOnTop(): void
    {
        $request = Mockery::mock(Request::class);
        $exception = new Exception();
        $verbose = new DebugDisplayer();
        $standard = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new VerboseFilter(true))->filter([$verbose, $standard], $request, $exception, $exception, 500);

        self::assertSame([$verbose, $standard], $displayers);
    }

    public function testDebugIsRemoved(): void
    {
        $request = Mockery::mock(Request::class);
        $exception = new Exception();
        $verbose = new DebugDisplayer();
        $standard = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new VerboseFilter(false))->filter([$verbose, $standard], $request, $exception, $exception, 500);

        self::assertSame([$standard], $displayers);
    }

    public function testNoChangeInDebugMode(): void
    {
        $request = Mockery::mock(Request::class);
        $exception = new Exception();

        $assets = function ($path) {
            return 'https://example.com/'.ltrim($path, '/');
        };

        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));
        $html = new HtmlDisplayer(new NullInformation(new InformationMerger()), $assets, 'foo');

        $displayers = (new VerboseFilter(true))->filter([$json, $html], $request, $exception, $exception, 500);

        self::assertSame([$json, $html], $displayers);
    }

    public function testNoChangeNotInDebugMode(): void
    {
        $request = Mockery::mock(Request::class);
        $exception = new Exception();
        $json = new JsonDisplayer(new NullInformation(new InformationMerger()));

        $displayers = (new VerboseFilter(false))->filter([$json], $request, $exception, $exception, 500);

        self::assertSame([$json], $displayers);
    }
}
