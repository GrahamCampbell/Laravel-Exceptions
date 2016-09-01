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
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * This is the query transformer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class QueryTransformer implements TransformerInterface
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
        if ($exception instanceof QueryException) {
            if($exception->getCode() == 23000){ // Integrity constraint violation
                $exception = new ConflictHttpException($exception->getMessage(), $exception, $exception->getCode());
            }
            else {
                $exception = new BadRequestHttpException($exception->getMessage(), $exception); // getCode can have letters, e.g. 42S22 so cannot go in.
            }
        }
        return $exception;
    }
}
