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

namespace GrahamCampbell\Exceptions\Displayer;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This is the displayer interface.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
interface DisplayerInterface
{
    /**
     * Get the error response associated with the given exception.
     *
     * @param \Throwable $exception
     * @param string     $id
     * @param int        $code
     * @param string[]   $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display(Throwable $exception, string $id, int $code, array $headers): Response;

    /**
     * Get the supported content type.
     *
     * @return string
     */
    public function contentType(): string;

    /**
     * Can we display the exception?
     *
     * @param \Throwable $original
     * @param \Throwable $transformed
     * @param int        $code
     *
     * @return bool
     */
    public function canDisplay(Throwable $original, Throwable $transformed, int $code): bool;

    /**
     * Do we provide verbose information about the exception?
     *
     * @return bool
     */
    public function isVerbose(): bool;
}
