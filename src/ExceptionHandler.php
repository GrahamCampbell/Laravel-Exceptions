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

namespace GrahamCampbell\Exceptions;

use Exception;
use GrahamCampbell\Exceptions\Displayer\DisplayerInterface;
use GrahamCampbell\Exceptions\Identifier\IdentifierInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as HandlerInterface;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

/**
 * This is the exception handler class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ExceptionHandler implements HandlerInterface
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected Container $container;

    /**
     * The exception config.
     *
     * @var array|null
     */
    protected ?array $config = null;

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
     * @param \Throwable $e
     *
     * @return void
     */
    public function report(Throwable $e): void
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
        $id = $this->container->make(IdentifierInterface::class)->identify($e);

        $logger->$level($e->getMessage(), ['identification' => ['id' => $id], 'exception' => $e]);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param \Throwable $e
     *
     * @return bool
     */
    public function shouldReport(Throwable $e): bool
    {
        return !$this->shouldntReport($e);
    }

    /**
     * Determine if the exception is in the do not report list.
     *
     * @param \Throwable $e
     *
     * @return bool
     */
    protected function shouldntReport(Throwable $e): bool
    {
        return !is_null(collect($this->dontReport)->first(function ($type) use ($e) {
            return $e instanceof $type;
        }));
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Throwable                                        $e
     *
     * @return void
     */
    public function renderForConsole($output, Throwable $e): void
    {
        (new ConsoleApplication())->renderThrowable($e, $output);
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

        return Arr::get($this->config, $key);
    }

    /**
     * Get the exception level.
     *
     * @param \Throwable $exception
     *
     * @return string
     */
    protected function getLevel(Throwable $exception): string
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
     * @param \Throwable               $e
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function render($request, Throwable $e): SymfonyResponse
    {
        $transformed = $this->getTransformed($e);

        $response = $e instanceof Responsable ? $e->toResponse($request) : null;

        if (!$response && ($e instanceof HttpResponseException)) {
            $response = $e->getResponse();
        }

        if (!$response instanceof SymfonyResponse) {
            try {
                $response = $this->getResponse($request, $e, $transformed);
            } catch (Throwable $e) {
                $this->report($e);

                $response = new Response('Internal server error.', 500, ['Content-Type' => 'text/plain']);
            }
        }

        return $this->toIlluminateResponse($response, $transformed);
    }

    /**
     * Map exception into an illuminate response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Throwable                                 $e
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function toIlluminateResponse($response, Throwable $e): SymfonyResponse
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
     * @param \Throwable               $transformed
     * @param \Throwable               $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse(Request $request, Throwable $exception, Throwable $transformed): SymfonyResponse
    {
        $id = $this->container->make(IdentifierInterface::class)->identify($exception);

        $flattened = FlattenException::createFromThrowable($transformed);
        $code = $flattened->getStatusCode();
        $headers = $flattened->getHeaders();

        return $this->getDisplayer($request, $exception, $transformed, $code)->display($transformed, $id, $code, $headers);
    }

    /**
     * Get the transformed exception.
     *
     * @param \Throwable $exception
     *
     * @return \Throwable
     */
    protected function getTransformed(Throwable $exception): Throwable
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
     * @param \Throwable               $original
     * @param \Throwable               $transformed
     * @param int                      $code
     *
     * @return \GrahamCampbell\Exceptions\Displayer\DisplayerInterface
     */
    protected function getDisplayer(
        Request $request,
        Throwable $original,
        Throwable $transformed,
        int $code
    ): DisplayerInterface {
        $displayers = $this->make((array) $this->getConfigItem('displayers'));

        if ($filtered = $this->getFiltered($displayers, $request, $original, $transformed, $code)) {
            return $filtered[0];
        }

        return $this->container->make($this->getConfigItem('default'));
    }

    /**
     * Get the filtered list of displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayer\DisplayerInterface[] $displayers
     * @param \Illuminate\Http\Request                                  $request
     * @param \Throwable                                                $original
     * @param \Throwable                                                $transformed
     * @param int                                                       $code
     *
     * @return \GrahamCampbell\Exceptions\Displayer\DisplayerInterface[]
     */
    protected function getFiltered(
        array $displayers,
        Request $request,
        Throwable $original,
        Throwable $transformed,
        int $code
    ): array {
        foreach ($this->make((array) $this->getConfigItem('filters')) as $filter) {
            $displayers = $filter->filter($displayers, $request, $original, $transformed, $code);
        }

        return array_values($displayers);
    }

    /**
     * Make multiple objects using the container.
     *
     * @param string[] $classes
     *
     * @return object[]
     */
    protected function make(array $classes): array
    {
        foreach ($classes as $index => $class) {
            try {
                $classes[$index] = $this->container->make($class);
            } catch (Throwable $e) {
                unset($classes[$index]);
                $this->report($e);
            }
        }

        return array_values($classes);
    }
}
