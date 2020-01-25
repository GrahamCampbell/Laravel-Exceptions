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

namespace GrahamCampbell\Exceptions\Transformer;

use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * This is the bad headers transformer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
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
    public function transform(Throwable $exception)
    {
        if ($exception instanceof ConflictingHeadersException) {
            $exception = new BadRequestHttpException('Bad headers provided.', $exception, $exception->getCode());
        }

        return $exception;
    }
}
