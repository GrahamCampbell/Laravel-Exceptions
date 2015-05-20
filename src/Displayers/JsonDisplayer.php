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
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * This is the json displayer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class JsonDisplayer implements DisplayerInterface
{
    /**
     * Get the error response associated with the given exception.
     *
     * @param \Exception $exception
     * @param int        $code
     * @param string[]   $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Exception $exception, $code, array $headers)
    {
        $info = ExceptionInfo::generate($code, $exception->getMessage());

        $content = ['success' => false, 'code' => $info['code'], 'msg' => $info['extra']];

        return new JsonResponse($content, $code, array_merge($headers, ['Content-Type' => $this->contentType()]));
    }

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType()
    {
        return 'application/json';
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
        return false;
    }
}
