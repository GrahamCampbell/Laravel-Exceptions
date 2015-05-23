<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the exception hander class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class ExceptionHandler extends Handler
{
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

        parent::__construct($container->log);
    }

    /**
     * Render an exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        $flattened = FlattenException::create($e);
        $code = $flattened->getStatusCode();
        $headers = $flattened->getHeaders();

        if ($displayer = $this->getDisplayer($e)) {
            $response = $displayer->display($e, $code, $headers);
        } else {
            $content = 'An error has occurred and this resource cannot be displayed.';
            $response = new Response($content, 500, array_merge($headers, ['Content-Type' => 'text/plain']));
        }

        return $response;
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
