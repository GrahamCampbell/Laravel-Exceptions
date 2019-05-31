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

namespace GrahamCampbell\Exceptions\Transformers;

use Exception;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * This is the validation transformer class.
 *
 * @author Jovanial Ferez <vojalf@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class ValidationTransformer implements TransformerInterface
{
    /**
     * Transform the provided exception.
     *
     * @param \Exception $exception
     *
     * @return \Exception
     */
    public function transform(Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            $exception = new UnprocessableEntityHttpException(head($exception->errors()), $exception->getCode(), $exception);
        }

        return $exception;
    }
}
