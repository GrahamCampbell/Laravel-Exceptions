<?php

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions;

use Exception;

/**
 * This is the exception identifier class.
 *
 * Note that for performance reasons, we only store up to 16 identifications in
 * memory at a given time. This is to prevent us using all your memory when
 * using a daemon queue worker on laravel.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionIdentifier
{
    /**
     * The identification mappings.
     *
     * @var string[]
     */
    protected $indentification;

    /**
     * Identify the given exception.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    public function identify(Exception $exception)
    {
        $hash = spl_object_hash($exception);

        // if we know about the exception, return it's id
        if (isset($this->indentification[$hash])) {
            return $this->indentification[$hash];
        }

        // cleanup in preparation for the identification
        if (count($this->indentification) >= 16) {
            array_shift($this->indentification);
        }

        // generate, store, and return the id
        return $this->indentification[$hash] = $this->generate();
    }

    /**
     * Generate a new uuid.
     *
     * We're generating uuids according to the official v4 spec.
     *
     * @return string
     */
    protected function generate()
    {
        $hash = bin2hex(random_bytes(16));

        $timeHi = hexdec(substr($hash, 12, 4)) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= 4 << 12;

        $clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        $params = [substr($hash, 0, 8), substr($hash, 8, 4), sprintf('%04x', $timeHi), sprintf('%02x', $clockSeqHi), substr($hash, 18, 2), substr($hash, 20, 12)];

        return vsprintf('%08s-%04s-%04s-%02s%02s-%012s', $params);
    }
}
