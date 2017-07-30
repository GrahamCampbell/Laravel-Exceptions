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

namespace GrahamCampbell\Exceptions;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as HandlerInterface;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Exception\HttpResponseException as OldHttpResponseException;
use Illuminate\Http\Exceptions\HttpResponseException as NewHttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

/**
 * This is the exception handler class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionHandler implements HandlerInterface
{
    /**
     * The exception config.
     *
     * @var array|null
     */
    protected $config;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var string[]
     */
    protected $dontReport = [];

    /**
     * Create a new exception handler instance.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Report or log an exception.
     *
     * @param \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e;
        }

        $level = $this->getLevel($e);
        $id = $this->container->make(ExceptionIdentifier::class)->identify($e);

        $logger->$level($e, ['identification' => ['id' => $id]]);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    public function shouldReport(Exception $e)
    {
        return !$this->shouldntReport($e);
    }

    /**
     * Determine if the exception is in the do not report list.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    protected function shouldntReport(Exception $e)
    {
        return !is_null(collect($this->dontReport)->first(function ($type) use ($e) {
            return $e instanceof $type;
        }));
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception                                        $e
     *
     * @return void
     */
    public function renderForConsole($output, Exception $e)
    {
        (new ConsoleApplication())->renderException($e, $output);
    }

    /**
     * Get an item from the config.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getConfigItem(string $key)
    {
        if ($this->config === null) {
            $this->config = $this->container->config->get('exceptions', []);
        }

        return array_get($this->config, $key);
    }

    /**
     * Get the exception level.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    protected function getLevel(Exception $exception)
    {
        foreach ((array) $this->getConfigItem('levels') as $class => $level) {
            if ($exception instanceof $class) {
                return $level;
            }
        }

        return 'error';
    }

    /**
     * Render an exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $transformed = $this->getTransformed($e);

        $response = $e instanceof Responsable ? $e->toResponse($request) : null;

        if (!$response && ($e instanceof OldHttpResponseException || $e instanceof NewHttpResponseException)) {
            $response = $e->getResponse();
        }

        if (!$response instanceof SymfonyResponse) {
            try {
                $response = $this->getResponse($request, $e, $transformed);
            } catch (Throwable $e) {
                $this->report($e instanceof Exception ? $e : new FatalThrowableError($e));

                $response = new Response('Internal server error.', 500, ['Content-Type' => 'text/plain']);
            }
        }

        return $this->toIlluminateResponse($response, $transformed);
    }

    /**
     * Map exception into an illuminate response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception                                 $e
     *
     * @return \Illuminate\Http\Response
     */
    protected function toIlluminateResponse($response, Exception $e)
    {
        if (!$response instanceof Response) {
            if ($response instanceof SymfonyRedirectResponse) {
                $response = new RedirectResponse($response->getTargetUrl(), $response->getStatusCode(), $response->headers->all());
            } else {
                $response = new Response($response->getContent(), $response->getStatusCode(), $response->headers->all());
            }
        }

        return $response->withException($e);
    }

    /**
     * Get the approprate response object.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $transformed
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function getResponse(Request $request, Exception $exception, Exception $transformed)
    {
        $id = $this->container->make(ExceptionIdentifier::class)->identify($exception);

        $flattened = FlattenException::create($transformed);
        $code = $flattened->getStatusCode();
        $headers = $flattened->getHeaders();

        return $this->getDisplayer($request, $exception, $transformed, $code)->display($transformed, $id, $code, $headers);
    }

    /**
     * Get the transformed exception.
     *
     * @param \Exception $exception
     *
     * @return \Exception
     */
    protected function getTransformed(Exception $exception)
    {
        foreach ($this->make((array) $this->getConfigItem('transformers')) as $transformer) {
            $exception = $transformer->transform($exception);
        }

        return $exception;
    }

    /**
     * Get the displayer instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $original
     * @param \Exception               $transformed
     * @param int                      $code
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface
     */
    protected function getDisplayer(Request $request, Exception $original, Exception $transformed, $code)
    {
        $displayers = $this->make((array) $this->getConfigItem('displayers'));

        if ($filtered = $this->getFiltered($displayers, $request, $original, $transformed, $code)) {
            return $filtered[0];
        }

        return $this->container->make($this->getConfigItem('default'));
    }

    /**
     * Get the filtered list of displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Illuminate\Http\Request                                   $request
     * @param \Exception                                                 $original
     * @param \Exception                                                 $transformed
     * @param int                                                        $code
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    protected function getFiltered(array $displayers, Request $request, Exception $original, Exception $transformed, $code)
    {
        foreach ($this->make((array) $this->getConfigItem('filters')) as $filter) {
            $displayers = $filter->filter($displayers, $request, $original, $transformed, $code);
        }

        return array_values($displayers);
    }

    /**
     * Make multiple objects using the container.
     *
     * @param string [] $classes
     *
     * @return object[]
     */
    protected function make(array $classes)
    {
        foreach ($classes as $index => $class) {
            try {
                $classes[$index] = $this->container->make($class);
            } catch (Throwable $e) {
                unset($classes[$index]);
                $this->report($e instanceof Exception ? $e : new FatalThrowableError($e));
            }
        }

        return array_values($classes);
    }
}
