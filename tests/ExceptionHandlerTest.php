<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the exception handler test class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class ExceptionHandlerTest extends AbstractTestCase
{
    public function testBasicRender()
    {
        $handler = $this->app->make('GrahamCampbell\Exceptions\ExceptionHandler');
        $response = $handler->render($this->app->request, new Exception('Foo Bar.'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertTrue(str_contains($response->getContent(), 'Internal Server Error'));
        $this->assertFalse(str_contains($response->getContent(), 'Foo Bar.'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testNotFoundRender()
    {
        $handler = $this->app->make('GrahamCampbell\Exceptions\ExceptionHandler');
        $response = $handler->render($this->app->request, new NotFoundHttpException());

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertTrue(str_contains($response->getContent(), 'Not Found'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testJsonRender()
    {
        $this->app->request->headers->set('accept', 'application/json');

        $handler = $this->app->make('GrahamCampbell\Exceptions\ExceptionHandler');
        $response = $handler->render($this->app->request, new GoneHttpException());

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(410, $response->getStatusCode());
        $this->assertSame('{"errors":[{"status":410,"title":"Gone","detail":"The requested resource is no longer available and will not be available again."}]}', $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testBadRender()
    {
        $this->app->request->headers->set('accept', 'not/acceptable');

        $handler = $this->app->make('GrahamCampbell\Exceptions\ExceptionHandler');
        $response = $handler->render($this->app->request, new NotFoundHttpException());

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
        $this->assertNull($response->headers->get('Content-Type'));
    }
}
