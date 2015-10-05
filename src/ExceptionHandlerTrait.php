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
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the exception hander trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait ExceptionHandlerTrait
{
    /**
     * The exception config.
     *
     * @var array
     */
    protected $config;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

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
            $level = $this->getLevel($e);
            $id = $this->container->make(ExceptionIdentifier::class)->identify($e);
            $this->log->{$level}($e, ['identification' => ['id' => $id]]);
        }
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
        $id = $this->container->make(ExceptionIdentifier::class)->identify($e);
        $transformed = $this->getTransformed($e);
        $flattened = FlattenException::create($transformed);
        $code = $flattened->getStatusCode();
        $headers = $flattened->getHeaders();

        $response = $this->getDisplayer($e, $transformed, $code)->display($transformed, $id, $code, $headers);

        return $this->toIlluminateResponse($response, $transformed);
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
     * @param \Exception $original
     * @param \Exception $transformed
     * @param int        $code
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface
     */
    protected function getDisplayer(Exception $original, Exception $transformed, $code)
    {
        $displayers = $this->make(array_get($this->config, 'displayers', []));

        if ($filtered = $this->getFiltered($displayers, $original, $transformed, $code)) {
            return $filtered[0];
        }

        return $this->container->make(array_get($this->config, 'default'));
    }

    /**
     * Get the filtered list of displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Exception                                                 $original
     * @param \Exception                                                 $transformed
     * @param int                                                        $code
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    protected function getFiltered(array $displayers, Exception $original, Exception $transformed, $code)
    {
        foreach ($this->make(array_get($this->config, 'filters', [])) as $filter) {
            $displayers = $filter->filter($displayers, $original, $transformed, $code);
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
