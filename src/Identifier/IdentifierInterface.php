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

namespace GrahamCampbell\Exceptions\Identifier;

use Throwable;

/**
 * This is the identifier interface.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
interface IdentifierInterface
{
    /**
     * Identify the given exception.
     *
     * @param \Throwable $exception
     *
     * @return string
     */
    public function identify(Throwable $exception);
}
