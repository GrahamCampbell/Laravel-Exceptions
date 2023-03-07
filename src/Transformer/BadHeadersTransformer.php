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

use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * This is the bad headers transformer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class BadHeadersTransformer implements TransformerInterface
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
        if ($exception instanceof ConflictingHeadersException) {
            $originalCode = $exception->getCode();
            $exception = new BadRequestHttpException(
                'Bad headers provided.',
                $exception,
                is_int($originalCode) ? $originalCode : 0,
            );
        }

        return $exception;
    }
}
