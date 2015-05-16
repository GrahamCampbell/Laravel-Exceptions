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
    private function render($info)
    {
        $content = file_get_contents('resources/plain.html');

        $info['home_url'] = asset('/');

        foreach ($info as $key => $val) {
            $content = str_replace("{{ $$key }}", $val, $content);
        }

        return $content;
    }
}
