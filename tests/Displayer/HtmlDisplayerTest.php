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

namespace GrahamCampbell\Tests\Exceptions\Displayer;

use Exception;
use GrahamCampbell\Exceptions\Displayer\HtmlDisplayer;
use GrahamCampbell\Exceptions\Information\InformationFactory;
use GrahamCampbell\Tests\Exceptions\AbstractTestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This is the html displayer test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class HtmlDisplayerTest extends AbstractTestCase
{
    public function testServerError()
    {
        $displayer = $this->getHtmlDisplayer();

        $response = $displayer->display(new HttpException(502, 'Oh noes!'), 'foo', 502, []);

        $expected = file_get_contents(__DIR__.'/stubs/502-html.txt');

        $this->assertSame($expected, $response->getContent());
        $this->assertSame(502, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testClientError()
    {
        $displayer = $this->getHtmlDisplayer();

        $response = $displayer->display(new HttpException(404, 'Arghhhh!'), 'bar', 404, []);

        $expected = file_get_contents(__DIR__.'/stubs/404-html.txt');

        $this->assertSame($expected, $response->getContent());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testProperties()
    {
        $displayer = $this->getHtmlDisplayer();

        $this->assertFalse($displayer->isVerbose());
        $this->assertTrue($displayer->canDisplay(new Exception(), new HttpException(500), 500));
        $this->assertSame('text/html', $displayer->contentType());
    }

    protected function getHtmlDisplayer()
    {
        $info = InformationFactory::create(__DIR__.'/../../resources/errors.json');

        $assets = function ($path) {
            return 'https://example.com/'.ltrim($path, '/');
        };

        return new HtmlDisplayer($info, $assets, __DIR__.'/../../resources/error.html');
    }
}
