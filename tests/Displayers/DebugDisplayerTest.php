<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions\Displayers;

use Exception;
use GrahamCampbell\Exceptions\Displayers\DebugDisplayer;
use GrahamCampbell\TestBench\AbstractTestCase;

/**
 * This is the debug displayer test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class DebugDisplayerTest extends AbstractTestCase
{
    public function testServerError()
    {
        $displayer = new DebugDisplayer();

        $response = $displayer->display(new Exception('Down for maintenance!'), 503, []);

        $this->assertInternalType('string', $response->getContent());
        $this->assertSame(503, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testClientError()
    {
        $displayer = new DebugDisplayer();

        $response = $displayer->display(new Exception('Naughty!'), 403, []);

        $this->assertInternalType('string', $response->getContent());
        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testProperties()
    {
        $displayer = new DebugDisplayer();

        $this->assertTrue($displayer->isVerbose());
        $this->assertTrue($displayer->canDisplay(new Exception()));
        $this->assertSame('text/html', $displayer->contentType());
    }
}
