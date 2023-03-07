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

namespace GrahamCampbell\Tests\Exceptions\Displayer;

use Exception;
use GrahamCampbell\Exceptions\Displayer\HtmlDisplayer;
use GrahamCampbell\Exceptions\Information\InformationFactory;
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\Tests\Exceptions\AbstractTestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This is the html displayer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class HtmlDisplayerTest extends AbstractTestCase
{
    public function testServerError(): void
    {
        $displayer = self::getHtmlDisplayer();

        $response = $displayer->display(new HttpException(502, 'Oh noes!'), 'foo', 502, []);

        $expected = file_get_contents(__DIR__.'/stubs/502-html.txt');

        self::assertSame($expected, $response->getContent());
        self::assertSame(502, $response->getStatusCode());
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testClientError(): void
    {
        $displayer = self::getHtmlDisplayer();

        $response = $displayer->display(new HttpException(404, 'Arghhhh!'), 'bar', 404, []);

        $expected = file_get_contents(__DIR__.'/stubs/404-html.txt');

        self::assertSame($expected, $response->getContent());
        self::assertSame(404, $response->getStatusCode());
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testProperties(): void
    {
        $displayer = self::getHtmlDisplayer();

        self::assertFalse($displayer->isVerbose());
        self::assertTrue($displayer->canDisplay(new Exception(), new HttpException(500), 500));
        self::assertSame('text/html', $displayer->contentType());
    }

    private static function getHtmlDisplayer(): HtmlDisplayer
    {
        $info = (new InformationFactory(new InformationMerger()))
            ->create(__DIR__.'/../../resources/lang/en/errors.json');

        $assets = function ($path) {
            return 'https://example.com/'.ltrim($path, '/');
        };

        return new HtmlDisplayer($info, $assets, __DIR__.'/../../resources/lang/en/error.html');
    }
}
