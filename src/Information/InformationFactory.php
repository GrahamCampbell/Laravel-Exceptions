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

namespace GrahamCampbell\Exceptions\Information;

/**
 * This is the information factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
final class InformationFactory
{
    /**
     * Create a new information instance.
     *
     * @param string|null $path
     *
     * @return \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    public static function create(string $path = null)
    {
        $data = $path === null ? null : self::getDecodedContents($path);

        if ($data === null) {
            return new NullInformation();
        }

        return new ArrayInformation($data);
    }

    /**
     * Get the decoded file contents, if possible.
     *
     * Returns null if the file could not be read, or did not contain JSON.
     *
     * @param string $path
     *
     * @return array|null
     */
    private static function getDecodedContents(string $path)
    {
        $contents = @file_get_contents($path);

        if (!is_string($contents)) {
            return null;
        }

        $decoded = @json_decode($contents, true);

        if (!is_array($decoded)) {
            return null;
        }

        return $decoded;
    }
}
