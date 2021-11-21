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

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

/**
 * This is the validation transformer class.
 *
 * @author Jovanial Ferez <vojalf@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class ValidationTransformer implements TransformerInterface
{
    /**
     * Transform the provided exception.
     *
     * @param \Throwable $exception
     *
     * @return \Throwable
     */
    public function transform(Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $exception = new UnprocessableEntityHttpException(head(head($exception->errors())), $exception, $exception->getCode());
        }

        return $exception;
    }
}
