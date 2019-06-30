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
use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is the bad headers transformer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class BadHeadersTransformer implements TransformerInterface
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
        if ($exception instanceof ConflictingHeadersException) {
            $exception = new BadRequestHttpException('Bad headers provided.', $exception, $exception->getCode());
        }

        return $exception;
    }
}
