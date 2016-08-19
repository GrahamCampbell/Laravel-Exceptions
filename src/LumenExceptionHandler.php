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
use Illuminate\Http\Response;
use Laravel\Lumen\Exceptions\Handler;

/**
 * This is the lumen exception hander class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class LumenExceptionHandler extends Handler
{
    use ExceptionHandlerTrait;

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
        $this->config = $container->config->get('exceptions', []);
        $this->container = $container;
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
        $response = new Response($response->getContent(), $response->getStatusCode(), $response->headers->all());

        $response->exception = $e;

        return $response;
    }
}
