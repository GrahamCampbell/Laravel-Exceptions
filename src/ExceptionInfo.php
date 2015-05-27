<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@cachethq.io>
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
 * @author Graham Campbell <graham@cachethq.io>
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
     * @param int        $code
     *
     * @return array
     */
    public function generate(Exception $exception, $code)
    {
        $errors = json_decode(file_get_contents($this->path), true);

        if (isset($errors[$code])) {
            $info = array_merge(['code' => $code], $errors[$code]);
        } else {
            $info = array_merge(['code' => 500], $errors[500]);
        }

        $msg = (string) $exception->getMessage();

        if ($exception instanceof HttpExceptionInterface && strlen($msg) < 36 && strlen($msg) > 4) {
            $info['extra'] = $msg;
        } else {
            $info['extra'] = 'Houston, We Have A Problem.';
        }

        return $info;
    }
}
