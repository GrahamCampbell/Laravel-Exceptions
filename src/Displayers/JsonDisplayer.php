<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
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
 * @author Graham Campbell <graham@alt-three.com>
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
     * Create a new json displayer instance.
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
     * @param string     $id
     * @param int        $code
     * @param string[]   $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Exception $exception, $id, $code, array $headers)
    {
        $info = $this->info->generate($exception, $id, $code);

        $error = ['id' => $id, 'status' => $info['code'], 'title' => $info['name'], 'detail' => $info['detail']];

        return new JsonResponse(['errors' => [$error]], $code, array_merge($headers, ['Content-Type' => $this->contentType()]));
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
     * @param \Exception $original
     * @param \Exception $transformed
     * @param int        $code
     *
     * @return bool
     */
    public function canDisplay(Exception $original, Exception $transformed, $code)
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
