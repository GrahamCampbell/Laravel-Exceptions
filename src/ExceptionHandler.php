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
use GrahamCampbell\Exceptions\Displayers\DisplayerInterface;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface as Log;
use Symfony\Component\Debug\Exception\FlattenException;
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
        $headers = $flattened->getHeaders();

        if ($displayer = $this->getDisplayer($request, $e)) {
            $response = (new $displayer())->display($e, $code, $headers);
        } else {
            $content = 'An error has occurred and this resource cannot be displayed.';
            $response = new Response($content, $code, array_merge($headers, ['Content-Type' => 'text/plain']));
        }

        return $response;
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

        if ($filtered = $this->getFiltered($displayers, $request, $e)) {
            return $filtered[0];
        }
    }

    /**
     * Get the filtered list of displayers.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[] $displayers
     * @param \Illuminate\Http\Request                                   $request
     * @param \Exception                                                 $e
     *
     * @return \GrahamCampbell\Exceptions\Displayers\DisplayerInterface[]
     */
    protected function getFiltered(array $displayers, Request $request, Exception $e)
    {
        $acceptable = $request->getAcceptableContentTypes();

        foreach ($displayers as $index => $displayer) {
            if (!$displayer->canDisplay($request, $e)) {
                unset($displayers[$index]);
                continue;
            }

            foreach ($this->getContentTypes($displayer) as $type) {
                if (in_array($type, $acceptable)) {
                    continue 2;
                }
            }

            unset($displayers[$index]);
        }

        return array_values($displayers);
    }

    /**
     * Get the content types to match.
     *
     * @param \GrahamCampbell\Exceptions\Displayers\DisplayerInterface $displayer
     *
     * @return string[]
     */
    protected function getContentTypes(DisplayerInterface $displayer)
    {
        $type = $displayer->contentType();

        return ['*/*', $type, strtok($type, '/').'/*'];
    }
}
