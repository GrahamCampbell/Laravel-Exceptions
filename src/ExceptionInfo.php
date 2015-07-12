<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * This is the exception info class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionInfo
{
    /**
     * The error info path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a exception info instance.
     *
     * @param string $path
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Get the exception information.
     *
     * @param \Exception $exception
     * @param string     $id
     * @param int        $code
     *
     * @return array
     */
    public function generate(Exception $exception, $id, $code)
    {
        $errors = json_decode(file_get_contents($this->path), true);

        if (isset($errors[$code])) {
            $info = array_merge(['id' => $id, 'code' => $code], $errors[$code]);
        } else {
            $info = array_merge(['id' => $id, 'code' => 500], $errors[500]);
        }

        if ($exception instanceof HttpExceptionInterface) {
            $msg = (string) $exception->getMessage();
            $info['detail'] = (strlen($msg) > 4) ? $msg : $info['message'];
            $info['summary'] = (strlen($msg) < 36 && strlen($msg) > 4) ? $msg : 'Houston, We Have A Problem.';
        } else {
            $info['detail'] = $info['message'];
            $info['summary'] = 'Houston, We Have A Problem.';
        }

        unset($info['message']);

        return $info;
    }
}
