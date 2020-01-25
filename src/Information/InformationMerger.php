<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Information;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * This is the information merger class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class InformationMerger
{
    /**
     * Merge the exception information.
     *
     * @param array      $info
     * @param \Throwable $exception
     *
     * @return array
     */
    public static function merge(array $info, Throwable $exception)
    {
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
