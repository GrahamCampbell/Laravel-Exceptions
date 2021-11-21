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

namespace GrahamCampbell\Exceptions\Information;

use Throwable;

/**
 * This is the information interface.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
interface InformationInterface
{
    /**
     * Get the exception information.
     *
     * @param \Throwable $exception
     * @param string     $id
     * @param int        $code
     *
     * @return array
     */
    public function generate(Throwable $exception, string $id, int $code);
}
