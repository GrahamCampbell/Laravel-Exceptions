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

use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

/**
 * This is the auth transformer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class AuthTransformer implements TransformerInterface
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
        if ($exception instanceof AuthorizationException) {
            $originalCode = $exception->getCode();
            $exception = new AccessDeniedHttpException(
                $exception->getMessage(),
                $exception,
                is_int($originalCode) ? $originalCode : 0,
            );
        }

        return $exception;
    }
}
