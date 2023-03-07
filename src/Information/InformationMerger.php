<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class InformationMerger implements MergerInterface
{
    /**
     * Merge the exception information.
     *
     * @param array      $info
     * @param \Throwable $exception
     *
     * @return array
     */
    public function merge(array $info, Throwable $exception): array
    {
        if ($exception instanceof HttpExceptionInterface) {
            $msg = (string) $exception->getMessage();
            $info['detail'] = (strlen($msg) > 4) ? $msg : $info['message'];
        } else {
            $info['detail'] = $info['message'];
        }

        unset($info['message']);

        return $info;
    }
}
