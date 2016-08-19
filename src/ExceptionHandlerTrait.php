<?php

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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the exception handler trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait ExceptionHandlerTrait
{
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

        $logger->{$level}($e, ['identification' => ['id' => $id]]);
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
        foreach (array_get($this->config, 'levels', []) as $class => $level) {
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

        $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;

        if (!$response instanceof Response) {
            try {
                $response = $this->getResponse($request, $e, $transformed);
            } catch (Exception $e) {
                $this->report($e);

                $response = new Response('Internal server error.', 500);
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
        if (!$response instanceof SymfonyRedirectResponse) {
            return $this->baseToIlluminateResponse($response, $e);
        }

        $response = new RedirectResponse($response->getTargetUrl(), $response->getStatusCode(), $response->headers->all());

        return method_exists($response, 'withException') ? $response->withException($e) : $response;
    }

    /**
     * Map exception into an illuminate response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception                                 $e
     *
     * @return \Illuminate\Http\Response
     */
    protected function baseToIlluminateResponse($response, Exception $e)
    {
        return parent::toIlluminateResponse($response, $e);
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
        foreach ($this->make(array_get($this->config, 'transformers', [])) as $transformer) {
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
        $displayers = $this->make(array_get($this->config, 'displayers', []));

        if ($filtered = $this->getFiltered($displayers, $request, $original, $transformed, $code)) {
            return $filtered[0];
        }

        return $this->container->make(array_get($this->config, 'default'));
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
        foreach ($this->make(array_get($this->config, 'filters', [])) as $filter) {
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
            } catch (Exception $e) {
                unset($classes[$index]);
                $this->report($e);
            }
        }

        return array_values($classes);
    }
}
