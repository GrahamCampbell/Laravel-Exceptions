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
    public function testBasicRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new Exception('Foo Bar.'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Internal Server Error'));
        $this->assertFalse(Str::contains($response->getContent(), 'Foo Bar.'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testHttpResponseExceptionRender()
    {
        $handler = $this->getExceptionHandler();

        $e = new HttpResponseException(new Response('Naughty!', 403, ['Content-Type' => 'text/plain']));

        $response = $handler->render($this->app->request, $e);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertSame('Naughty!', $response->getContent());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
    }

    public function testHttpRedirectResponseExceptionRender()
    {
        $handler = $this->getExceptionHandler();

        $e = new HttpResponseException(new SymfonyRedirectResponse('https://example.com/foo', 302));

        $response = $handler->render($this->app->request, $e);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(302, $response->getStatusCode());

        if (property_exists($response, 'exception')) {
            $this->assertSame($e, $response->exception);
        }
    }

    public function testNotFoundRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Not Found'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testBadHeadersExceptionRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new ConflictingHeadersException('Oh no!'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertInstanceOf(BadRequestHttpException::class, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Bad Request'));
        $this->assertTrue(Str::contains($response->getContent(), 'Bad headers provided.'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testBadHostExceptionRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new SuspiciousOperationException('Oh no!'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertInstanceOf(NotFoundHttpException::class, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Not Found'));
        $this->assertTrue(Str::contains($response->getContent(), 'Bad hostname provided.'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testAuthExceptionRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new AuthorizationException('This action is unauthorized.'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(403, $response->getStatusCode());
        $this->assertInstanceOf(AccessDeniedHttpException::class, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Forbidden'));
        $this->assertTrue(Str::contains($response->getContent(), 'This action is unauthorized.'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testCsrfExceptionRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new TokenMismatchException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertInstanceOf(BadRequestHttpException::class, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Bad Request'));
        $this->assertTrue(Str::contains($response->getContent(), 'CSRF token validation failed.'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testModelExceptionRender()
    {
        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new ModelNotFoundException('Model not found!'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertInstanceOf(NotFoundHttpException::class, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Not Found'));
        $this->assertTrue(Str::contains($response->getContent(), 'Model not found!'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testJsonRender()
    {
        $this->app->request->headers->set('accept', 'application/json');

        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new GoneHttpException());
        $id = $this->app->make(IdentifierInterface::class)->identify($e);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(410, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertSame('{"errors":[{"id":"'.$id.'","status":410,"title":"Gone","detail":"The requested resource is no longer available and will not be available again."}]}', $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testBadRender()
    {
        $this->app->request->headers->set('accept', 'not/acceptable');

        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertTrue(Str::contains($response->getContent(), 'Not Found'));
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
    }

    public function testRenderException()
    {
        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(InformationInterface::class);
            $assets = function ($path) {
                throw new RuntimeException('Oh no...');
            };
            $path = __DIR__.'/../resources/lang/en/error.html';

            return new HtmlDisplayer($info, $assets, realpath($path));
        });

        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertSame('Internal server error.', $response->getContent());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
    }

    public function testRenderThrowable()
    {
        $this->app->bind(HtmlDisplayer::class, function (Container $app) {
            $info = $app->make(InformationInterface::class);
            $assets = function ($path) {
                throw new TypeError('Foo.');
            };
            $path = __DIR__.'/../resources/lang/en/error.html';

            return new HtmlDisplayer($info, $assets, realpath($path));
        });

        $handler = $this->getExceptionHandler();
        $response = $handler->render($this->app->request, $e = new NotFoundHttpException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame($e, $response->exception);
        $this->assertSame('Internal server error.', $response->getContent());
        $this->assertSame('text/plain', $response->headers->get('Content-Type'));
    }

    public function testReportHttp()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new NotFoundHttpException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new Exception();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('error')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportFail()
    {
        $app = $this->app;

        if (version_compare($app::VERSION, '5.3') < 0) {
            return $this->markTestSkipped('Laravel version too old.');
        }

        $mock = Mockery::mock(LoggerInterface::class);

        $this->app->bind(LoggerInterface::class, function () {
            throw new RuntimeException('Foo.');
        });

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bar');

        $this->getExceptionHandler()->report(new InvalidArgumentException('Bar.'));
    }

    public function testReportFailThrowable()
    {
        $mock = Mockery::mock(LoggerInterface::class);

        $this->app->bind(LoggerInterface::class, function () {
            throw new TypeError('Foo.');
        });

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Foo');

        $this->getExceptionHandler()->report(new InvalidArgumentException('Baz.'));
    }

    public function testReportBadRequestException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new BadRequestHttpException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('warning')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportBadHeadersException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new ConflictingHeadersException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportBadHostException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new SuspiciousOperationException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportAuthException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new AuthorizationException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('warning')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportCsrfException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new TokenMismatchException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('notice')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportModelException()
    {
        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new ModelNotFoundException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('warning')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testReportFallbackWorks()
    {
        $this->app->config->set('exceptions.levels', [TokenMismatchException::class => 'notice']);

        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $e = new BadRequestHttpException();
        $id = $this->app->make(IdentifierInterface::class)->identify($e);
        $mock->shouldReceive('error')->once()->with($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);

        $this->assertNull($this->getExceptionHandler()->report($e));
    }

    public function testBadDisplayers()
    {
        $this->app->config->set('exceptions.displayers', ['Ooops']);

        $mock = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $mock);
        $mock->shouldReceive('error')->once();

        $response = $this->getExceptionHandler()->render($this->app->request, new Exception());

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testRenderForConsole()
    {
        $handler = $this->getExceptionHandler();

        $o = Mockery::mock(OutputInterface::class);
        $o->shouldReceive('writeln')->with('', OutputInterface::VERBOSITY_QUIET)->once();
        $o->shouldReceive('writeln')->with([
            '<comment>In ExceptionHandlerTest.php line 407:</comment>',
            '<error>                    </error>',
            '<error>  Model not found!  </error>',
            '<error>                    </error>',
            '',
        ], OutputInterface::VERBOSITY_QUIET)->once();
        $o->shouldReceive('getVerbosity')->andReturn(OutputInterface::VERBOSITY_NORMAL);

        $handler->renderForConsole($o, new ModelNotFoundException('Model not found!'));
    }

    protected function getExceptionHandler()
    {
        return $this->app->make(ExceptionHandler::class);
    }
}
