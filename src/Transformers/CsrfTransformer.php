<?php

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
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the csrf transformer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CsrfTransformer implements TransformerInterface
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
        if ($exception instanceof TokenMismatchException) {
            $message = $exception->getMessage();
            $exception = new BadRequestHttpException($message ?: 'CSRF token validation failed.');
        }

        return $exception;
    }
}
