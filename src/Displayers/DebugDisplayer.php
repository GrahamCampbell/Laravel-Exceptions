<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\PrettyPageHandler as Handler;
use Whoops\Run as Whoops;

/**
 * This is the debug displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class DebugDisplayer implements DisplayerInterface
{
    /**
     * Get the error response associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Exception $exception, $code)
    {
        $content = $this->whoops()->handleException($exception);

        return new Response($content, $code);
    }

    /**
     * Get the whoops instance.
     *
     * @return \Whoops\Run
     */
    protected function whoops()
    {
        $whoops = new Whoops();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new Handler());

        return $whoops;
    }

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType()
    {
        return 'text/html';
    }

    /**
     * Can we display the exception in the given context?
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return bool
     */
    public function canDisplay(Request $request, Exception $exception)
    {
        return true;
    }

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose()
    {
        return true;
    }
}
