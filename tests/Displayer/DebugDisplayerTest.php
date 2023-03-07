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
use GrahamCampbell\Exceptions\Displayer\DebugDisplayer;
use GrahamCampbell\TestBench\AbstractTestCase;

/**
 * This is the debug displayer test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class DebugDisplayerTest extends AbstractTestCase
{
    public function testServerError(): void
    {
        $displayer = new DebugDisplayer();

        $response = $displayer->display(new Exception('Down for maintenance!'), 'foo', 503, []);

        self::assertSame(503, $response->getStatusCode());
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testClientError(): void
    {
        $displayer = new DebugDisplayer();

        $response = $displayer->display(new Exception('Naughty!'), 'bar', 403, []);

        self::assertSame(403, $response->getStatusCode());
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testProperties(): void
    {
        $exception = new Exception();
        $displayer = new DebugDisplayer();

        self::assertTrue($displayer->isVerbose());
        self::assertTrue($displayer->canDisplay($exception, $exception, 500));
        self::assertSame('text/html', $displayer->contentType());
    }
}
