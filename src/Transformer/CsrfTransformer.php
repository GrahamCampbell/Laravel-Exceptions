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

namespace GrahamCampbell\Exceptions\Transformer;

use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * This is the csrf transformer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class CsrfTransformer implements TransformerInterface
{
    /**
     * Transform the provided exception.
     *
     * @param \Throwable $exception
     *
     * @return \Throwable
     */
    public function transform(Throwable $exception): Throwable
    {
        if ($exception instanceof TokenMismatchException) {
            $originalCode = $exception->getCode();
            $exception = new BadRequestHttpException(
                $exception->getMessage() ?: 'CSRF token validation failed.',
                $exception,
                is_int($originalCode) ? $originalCode : 0,
            );
        }

        return $exception;
    }
}
