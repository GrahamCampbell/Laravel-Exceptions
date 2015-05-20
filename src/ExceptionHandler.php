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

        if ($displayer) {
            $content = (new $displayer())->display($e, $code);
        } else {
            $content = 'An error has occurred and this resource cannot be displayed.';
        }

        $headers = $flattened->getHeaders();

        if (is_array($content)) {
            return new JsonResponse($content, $code, $headers);
        }

        return new Response($content, $code, $headers);
    }

    /**
     * Get the displayer instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface|null
     */
    protected function getDisplayer(Request $request, Exception $e)
    {
        $displayers = $this->config->get('exceptions.displayers', []);

        foreach ($displayers as $index => $displayer) {
            $displayers[$index] = new $displayer();
        }

        if ($this->config->get('app.debug') !== true) {
            foreach ($displayers as $index => $displayer) {
                if ($displayer->isVerbose()) {
                    unset($displayers[$index]);
                }
            }
        }

        foreach ($displayers as $index => $displayer) {
            if (!$displayer->canDisplay($request, $e)) {
                unset($displayers[$index]);
            }
        }

        if ($remaining = array_values($displayers)) {
            return $remaining[0];
        }
    }
}
