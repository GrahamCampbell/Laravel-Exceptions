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

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * This is the model transformer class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class ModelTransformer implements TransformerInterface
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
        if ($exception instanceof ModelNotFoundException) {
            $originalCode = $exception->getCode();
            $exception = new NotFoundHttpException(
                $exception->getMessage(),
                $exception,
                is_int($originalCode) ? $originalCode : 0,
            );
        }

        return $exception;
    }
}
