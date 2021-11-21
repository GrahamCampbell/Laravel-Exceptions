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

/**
 * This is the factory interface.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
interface FactoryInterface
{
    /**
     * Create a new information instance.
     *
     * @param string|null $path
     *
     * @return \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    public function create(string $path = null);
}
