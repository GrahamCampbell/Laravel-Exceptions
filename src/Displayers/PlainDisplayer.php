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
use GrahamCampbell\Exceptions\ExceptionInfo;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

/**
 * This is the plain displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class PlainDisplayer implements DisplayerInterface
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
        $info = ExceptionInfo::generate($code, $exception->getMessage());

        return $this->render($info);
    }

    /**
     * Render the page with given info.
     *
     * @param array $info
     *
     * @return string
     */
    protected function render(array $info)
    {
        $content = file_get_contents(__DIR__.'/resources/plain.html');

        $info['home_url'] = asset('/');
        $info['favicon_url'] = asset('favicon.ico');

        foreach ($info as $key => $val) {
            $content = str_replace("{{ $$key }}", $val, $content);
        }

        return $content;
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
        return true;
    }
}
