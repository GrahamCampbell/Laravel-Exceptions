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

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Psr\Log\LoggerInterface;

/**
 * This is the exception handler class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionHandler extends Handler
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

        parent::__construct($container->make(LoggerInterface::class));
    }
}
