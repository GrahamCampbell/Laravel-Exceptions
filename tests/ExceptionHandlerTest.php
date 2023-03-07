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

namespace GrahamCampbell\Tests\Exceptions;

use Exception;
use GrahamCampbell\Exceptions\Displayer\HtmlDisplayer;
use GrahamCampbell\Exceptions\ExceptionHandler;
use GrahamCampbell\Exceptions\Identifier\IdentifierInterface;
use GrahamCampbell\Exceptions\Information\InformationInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Mockery;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TypeError;

/**
 * This is the exception handler test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ExceptionHandlerTest extends AbstractTestCase
{
    public function testBasicRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new Exception('Foo Bar.'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(500, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Internal Server Error'));
        self::assertFalse(Str::contains($response->getContent(), 'Foo Bar.'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testHttpResponseExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        $e = new HttpResponseException(new Response('Naughty!', 403, ['Content-Type' => 'text/plain']));

        $response = $handler->render($this->app->request, $e);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(403, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertSame('Naughty!', $response->getContent());
        self::assertSame('text/plain', $response->headers->get('Content-Type'));
    }

    public function testHttpRedirectResponseExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        $e = new HttpResponseException(new SymfonyRedirectResponse('https://example.com/foo', 302));

        $response = $handler->render($this->app->request, $e);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame(302, $response->getStatusCode());

        if (property_exists($response, 'exception')) {
            self::assertSame($e, $response->exception);
        }
    }

    public function testNotFoundRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(404, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Not Found'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testBadHeadersExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new ConflictingHeadersException('Oh no!'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(400, $response->getStatusCode());
        self::assertInstanceOf(BadRequestHttpException::class, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Bad Request'));
        self::assertTrue(Str::contains($response->getContent(), 'Bad headers provided.'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testBadHostExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new SuspiciousOperationException('Oh no!'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(404, $response->getStatusCode());
        self::assertInstanceOf(NotFoundHttpException::class, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Not Found'));
        self::assertTrue(Str::contains($response->getContent(), 'Bad hostname provided.'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testAuthExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new AuthorizationException('This action is unauthorized.'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(403, $response->getStatusCode());
        self::assertInstanceOf(AccessDeniedHttpException::class, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Forbidden'));
        self::assertTrue(Str::contains($response->getContent(), 'This action is unauthorized.'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testCsrfExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new TokenMismatchException());

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(400, $response->getStatusCode());
        self::assertInstanceOf(BadRequestHttpException::class, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Bad Request'));
        self::assertTrue(Str::contains($response->getContent(), 'CSRF token validation failed.'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testModelExceptionRender(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new ModelNotFoundException('Model not found!'));

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(404, $response->getStatusCode());
        self::assertInstanceOf(NotFoundHttpException::class, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Not Found'));
        self::assertTrue(Str::contains($response->getContent(), 'Model not found!'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testJsonRender(): void
    {
        $this->app->request->headers->set('accept', 'application/json');

        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new GoneHttpException());
        $id = $this->app->make(IdentifierInterface::class)->identify($e);

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(410, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertSame('{"errors":[{"id":"'.$id.'","status":410,"title":"Gone","detail":"The requested resource is no longer available and will not be available again."}]}', $response->getContent());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testBadRender(): void
    {
        $this->app->request->headers->set('accept', 'not/acceptable');

        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(404, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertTrue(Str::contains($response->getContent(), 'Not Found'));
        self::assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testRenderException(): void
    {
        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(InformationInterface::class);
            $assets = function ($path) {
                throw new RuntimeException('Oh no...');
            };
            $path = __DIR__.'/../resources/lang/en/error.html';

            return new HtmlDisplayer($info, $assets, realpath($path));
        });

        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(500, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertSame('Internal server error.', $response->getContent());
        self::assertSame('text/plain', $response->headers->get('Content-Type'));
    }

    public function testRenderThrowable(): void
    {
        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(InformationInterface::class);
            $assets = function ($path) {
                throw new TypeError('Foo.');
            };
            $path = __DIR__.'/../resources/lang/en/error.html';

            return new HtmlDisplayer($info, $assets, realpath($path));
        });

        $handler = $this->app->make(ExceptionHandler::class);
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        self::assertInstanceOf(Response::class, $response);
        self::assertSame(500, $response->getStatusCode());
        self::assertSame($e, $response->exception);
        self::assertSame('Internal server error.', $response->getContent());
        self::assertSame('text/plain', $response->headers->get('Content-Type'));
    }

    public function testReportHttp(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new NotFoundHttpException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new Exception();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('error')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportFailThrowable(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);

        $this->app->bind(LoggerInterface::class, function () {
            throw new TypeError('Foo.');
        });

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Foo');

        $this->app->make(ExceptionHandler::class)->report(new InvalidArgumentException('Baz.'));
    }

    public function testReportBadRequestException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new BadRequestHttpException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('warning')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportBadHeadersException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new ConflictingHeadersException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportBadHostException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new SuspiciousOperationException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportAuthException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new AuthorizationException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('warning')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportCsrfException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new TokenMismatchException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportModelException(): void
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new ModelNotFoundException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('warning')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testReportFallbackWorks(): void
    {
        $this->app->config->set('exceptions.levels', [TokenMismatchException::class => 'notice']);

        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new BadRequestHttpException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('error')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        self::assertNull($this->app->make(ExceptionHandler::class)->report($e));
    }

    public function testBadDisplayers(): void
    {
        $this->app->config->set('exceptions.displayers', ['Ooops']);

        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $mock->shouldReceive('error')->once();

        $response = $this->app->make(ExceptionHandler::class)->render($this->app->request, new Exception());

        self::assertInstanceOf(Response::class, $response);
    }

    public function testRenderForConsole(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        $o = Mockery::mock(OutputInterface::class);
        $o->shouldReceive('writeln')->twice();
        $o->shouldReceive('getVerbosity')->andReturn(OutputInterface::VERBOSITY_NORMAL);

        $handler->renderForConsole($o, new ModelNotFoundException('Model not found!'));
    }
}
