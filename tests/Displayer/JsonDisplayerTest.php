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

use GrahamCampbell\Exceptions\Displayer\JsonDisplayer;
use GrahamCampbell\Exceptions\Information\InformationFactory;
use GrahamCampbell\Exceptions\Information\InformationMerger;
use GrahamCampbell\TestBench\AbstractTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This is the json displayer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class JsonDisplayerTest extends AbstractTestCase
{
    public function testServerError(): void
    {
        $displayer = new JsonDisplayer((new InformationFactory(new InformationMerger()))
            ->create(__DIR__.'/../../resources/lang/en/errors.json'));

        $response = $displayer->display(new HttpException(500, 'Gutted!'), 'foo', 500, []);

        $expected = file_get_contents(__DIR__.'/stubs/500-json.txt');

        self::assertSame(trim($expected), $response->getContent());
        self::assertSame(500, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testClientError(): void
    {
        $displayer = new JsonDisplayer((new InformationFactory(new InformationMerger()))
            ->create(__DIR__.'/../../resources/lang/en/errors.json'));

        $response = $displayer->display(new HttpException(401, 'Grrrr!'), 'bar', 401, []);

        $expected = file_get_contents(__DIR__.'/stubs/401-json.txt');

        self::assertSame(trim($expected), $response->getContent());
        self::assertSame(401, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testProperties(): void
    {
        $displayer = new JsonDisplayer((new InformationFactory(new InformationMerger()))
            ->create(__DIR__.'/../../resources/lang/en/errors.json'));

        self::assertFalse($displayer->isVerbose());
        self::assertTrue($displayer->canDisplay(new InvalidArgumentException(), new HttpException(500), 500));
        self::assertSame('application/json', $displayer->contentType());
    }
}
