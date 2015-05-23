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
use GrahamCampbell\Exceptions\Displayers\JsonDisplayer;
use GrahamCampbell\Exceptions\ExceptionInfo;
use GrahamCampbell\TestBench\AbstractTestCase;

/**
 * This is the json displayer test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class JsonDisplayerTest extends AbstractTestCase
{
    public function testServerError()
    {
        $displayer = new JsonDisplayer(new ExceptionInfo(__DIR__.'/../../resources/errors.json'));

        $response = $displayer->display(new Exception('Gutted!'), 500, []);

        $expected = file_get_contents(__DIR__.'/stubs/500-json.txt');

        $this->assertSame(trim($expected), $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testClientError()
    {
        $displayer = new JsonDisplayer(new ExceptionInfo(__DIR__.'/../../resources/errors.json'));

        $response = $displayer->display(new Exception('Grrrr!'), 401, []);

        $expected = file_get_contents(__DIR__.'/stubs/401-json.txt');

        $this->assertSame(trim($expected), $response->getContent());
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testProperties()
    {
        $displayer = new JsonDisplayer(new ExceptionInfo(__DIR__.'/../../resources/errors.json'));

        $this->assertFalse($displayer->isVerbose());
        $this->assertTrue($displayer->canDisplay(new Exception()));
        $this->assertSame('application/json', $displayer->contentType());
    }
}
