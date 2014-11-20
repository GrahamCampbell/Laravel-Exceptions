<?php

/**
 * This file is part of Laravel Exceptions by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Exceptions;

use Exception;
use GrahamCampbell\Exceptions\Displayers\ArrayDisplayer;
use GrahamCampbell\Exceptions\Displayers\DebugDisplayer;
use GrahamCampbell\Exceptions\Displayers\PlainDisplayer;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Psr\Log\LoggerInterface as Log;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the exception hander class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Exceptions/blob/master/LICENSE.md> Apache 2.0
 */
class ExceptionHandler extends Handler
{
    /**
     * The config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Create a new exception handler instance.
     *
     * @param \Psr\Log\LoggerInterface                  $log
     * @param \Illuminate\Contracts\Config\Repository   $config
     * @param \Illuminate\Contracts\Container\Container $container
     *
     * @return void
     */
    public function __construct(Log $log, Config $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;

        parent::__construct($log);
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
        $ajax = $request->ajax();
        $debug = $this->config->get('app.debug');

        $content = $this->getContent($e, $code, $ajax, $debug);

        $headers = $flattened->getHeaders();

        if (is_array($content)) {
            return new JsonResponse($content, $code, $headers);
        }

        return new Response($content, $code, $headers);
    }

    /**
     * Get the content associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     * @param bool       $ajax
     * @param bool       $debug
     *
     * @return string|array
     */
    protected function getContent(Exception $exception, $code, $ajax, $debug)
    {
        if ($ajax) {
            return $this->container->make(ArrayDisplayer::class)->display($exception, $code);
        }

        if ($debug) {
            return $this->container->make(DebugDisplayer::class)->display($exception, $code);
        }

        return $this->container->make(PlainDisplayer::class)->display($exception, $code);
    }
}
