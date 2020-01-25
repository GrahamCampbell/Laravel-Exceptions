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

namespace GrahamCampbell\Exceptions\Identifier;

use Throwable;

/**
 * This is the hashing identifier class.
 *
 * Note that for performance reasons, by default, we only store up to 32
 * identifications in memory at a given time. This is to prevent us using all
 * your memory when using a daemon queue worker on laravel.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class HashingIdentifier implements IdentifierInterface
{
    /**
     * The identification mappings.
     *
     * @var string[]
     */
    private $identification = [];

    /**
     * The maximum identifications to remember.
     *
     * @var int
     */
    private $maximum;

    /**
     * Create a new hashing identifier instance.
     *
     * @param int|null $maximum
     *
     * @return void
     */
    public function __construct(int $maximum = null)
    {
        $this->maximum = $maximum ?? 32;
    }

    /**
     * Identify the given exception.
     *
     * @param \Throwable $exception
     *
     * @return string
     */
    public function identify(Throwable $exception)
    {
        $hash = spl_object_hash($exception);

        // if we know about the exception, return it's id
        if (isset($this->identification[$hash])) {
            return $this->identification[$hash];
        }

        // cleanup in preparation for the identification
        if (count($this->identification) >= $this->maximum) {
            array_shift($this->identification);
        }

        // generate, store, and return the id
        return $this->identification[$hash] = UuidV4Generator::generate();
    }
}
