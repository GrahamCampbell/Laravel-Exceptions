<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions;

use Exception;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as Log;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the exception hander class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
     * Create a new exception handler instance.
     *
     * @param \Psr\Log\LoggerInterface                $log
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return void
     */
    public function __construct(Log $log, Config $config)
    {
        $this->config = $config;

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

        $displayer = $this->getDisplayer($request, $e);

        $content = (new $displayer())->display($e, $code);

//        $this->getContent($e, $code, $ajax, $debug);

        $headers = $flattened->getHeaders();

        if (is_array($content)) {
            return new JsonResponse($content, $code, $headers);
        }

        return new Response($content, $code, $headers);
    }

    /**
     * Get the displayer class.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return mixed
     */
    protected function getDisplayer(Request $request, Exception $e)
    {
        $displayers = $this->config->get('exceptions.displayers');

        foreach ($displayers as $displayer) {
            if ((new $displayer())->canDisplay($e, $request, $this->config)) {
                return $displayer;
            }
        }

        return $this->getDefaultDisplayer();
    }

    /**
     * Get the default displayer class.
     *
     * @return mixed
     */
    protected function getDefaultDisplayer()
    {
        $displayers = $this->config->get('exceptions.displayers');

        $default = $this->config->get('exception.default');

        if (!isset($displayers[$default])) {
            throw new InvalidArgumentException('The default displayer can not be found.');
        }

        return $displayers[$default];
    }
}
