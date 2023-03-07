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
 * This is the information factory class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class InformationFactory implements FactoryInterface
{
    /**
     * The information merger.
     *
     * @var \GrahamCampbell\Exceptions\Information\MergerInterface
     */
    private MergerInterface $merger;

    /**
     * Create a new information factory instance.
     *
     * @param \GrahamCampbell\Exceptions\Information\MergerInterface $merger
     *
     * @return void
     */
    public function __construct(MergerInterface $merger)
    {
        $this->merger = $merger;
    }

    /**
     * Create a new information instance.
     *
     * @param string|null $path
     *
     * @return \GrahamCampbell\Exceptions\Information\InformationInterface
     */
    public function create(string $path = null): InformationInterface
    {
        $data = $path === null ? null : self::getDecodedContents($path);

        if ($data === null) {
            return new NullInformation($this->merger);
        }

        return new ArrayInformation($this->merger, $data);
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
    private static function getDecodedContents(string $path): ?array
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
