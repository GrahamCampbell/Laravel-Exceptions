<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Displayers;

use Exception;
use GrahamCampbell\Exceptions\ExceptionInfo;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * This is the json displayer class.
 *
 * @author Graham Campbell <graham@cachethq.io>
 */
class JsonDisplayer implements DisplayerInterface
{
    /**
     * The exception info instance.
     *
     * @var \GrahamCampbell\Exceptions\ExceptionInfo
     */
    protected $info;

    /**
     * Create a new html displayer instance.
     *
     * @param \GrahamCampbell\Exceptions\ExceptionInfo $info
     *
     * @return void
     */
    public function __construct(ExceptionInfo $info)
    {
        $this->info = $info;
    }

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
        $info = $this->info->generate($exception, $code);

        $content = ['status' => $info['code'], 'title' => $info['name'], 'detail' => $info['message']];

        return new JsonResponse($content, $code, array_merge($headers, ['Content-Type' => 'application/json']));
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
     * Can we display the exception?
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    public function canDisplay(Exception $exception)
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
