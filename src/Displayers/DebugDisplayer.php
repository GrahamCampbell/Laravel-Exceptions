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
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
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
     * Get the content associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     *
     * @return string
     */
    public function display(Exception $exception, $code)
    {
        return $this->whoops()->handleException($exception);
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
     * Can the exception be displayed?
     *
     * @param \Exception                              $exception
     * @param \Illuminate\Http\Request                $request
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return bool
     */
    public function canDisplay(Exception $exception, Request $request, Repository $config)
    {
        return $this->config->get('app.debug');
    }
}
