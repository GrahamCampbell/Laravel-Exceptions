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
    public function testServerError()
    {
        $displayer = new DebugDisplayer();

        $response = $displayer->display(new Exception('Down for maintenance!'), 'foo', 503, []);

        $this->assertSame(503, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testClientError()
    {
        $displayer = new DebugDisplayer();

        $response = $displayer->display(new Exception('Naughty!'), 'bar', 403, []);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testProperties()
    {
        $exception = new Exception();
        $displayer = new DebugDisplayer();

        $this->assertTrue($displayer->isVerbose());
        $this->assertTrue($displayer->canDisplay($exception, $exception, 500));
        $this->assertSame('text/html', $displayer->contentType());
    }
}
