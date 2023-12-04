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

/**
 * This is the UUID V4 generator class.
 *
 * @internal
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class UuidV4Generator
{
    /**
     * Generate a new uuid v4.
     *
     * @return string
     */
    public static function generate(): string
    {
        $hash = bin2hex(random_bytes(16));

        $timeHi = hexdec(substr($hash, 12, 4)) & 0x0FFF;
        $timeHi &= ~0xF000;
        $timeHi |= 4 << 12;

        $clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3F;
        $clockSeqHi &= ~0xC0;
        $clockSeqHi |= 0x80;

        $params = [
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            sprintf('%04x', $timeHi),
            sprintf('%02x', $clockSeqHi),
            substr($hash, 18, 2),
            substr($hash, 20, 12),
        ];

        return vsprintf('%08s-%04s-%04s-%02s%02s-%012s', $params);
    }
}
