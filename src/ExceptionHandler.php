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
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the exception hander class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionHandler extends Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        NotFoundHttpException::class,
    ];

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

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

        parent::__construct($container->make(LoggerInterface::class));
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
        if ($this->shouldReport($e)) {
            $id = $this->container->make(ExceptionIdentifier::class)->identify($e);
            $this->log->error($e, ['identification' => ['id' => $id]]);
        }
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
        $id = $this->container->make(ExceptionIdentifier::class)->identify($e);
        $e = $this->getTransformed($e);
        $flattened = FlattenException::create($e);
        $code = $flattened->getStatusCode();
        $headers = $flattened->getHeaders();

        $response = $this->getDisplayer($e)->display($e, $id, $code, $headers);

        return $this->toIlluminateResponse($response, $e);
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
        foreach ($this->make($this->container->config->get('exceptions.transformers', [])) as $transformer) {
            $exception = $transformer->transform($exception);
        }

        return $exception;
    }

    /**
     * Get the displayer instance.
     *
     * @param \Exception $exception
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface|null
     */
    protected function getDisplayer(Exception $exception)
    {
        $displayers = $this->make($this->container->config->get('exceptions.displayers', []));

        if ($filtered = $this->getFiltered($displayers, $exception)) {
            return $filtered[0];
        }

        return $this->container->make($this->container->config->get('exceptions.default'));
    }

    /**
     * Get the filtered list of displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Exception                                                 $exception
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    protected function getFiltered(array $displayers, Exception $exception)
    {
        foreach ($this->make($this->container->config->get('exceptions.filters', [])) as $filter) {
            $displayers = $filter->filter($displayers, $exception);
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
            $classes[$index] = $this->container->make($class);
        }

        return array_values($classes);
    }
}
